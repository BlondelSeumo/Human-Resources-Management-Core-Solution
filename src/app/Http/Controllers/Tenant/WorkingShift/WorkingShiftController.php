<?php

namespace App\Http\Controllers\Tenant\WorkingShift;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\WorkingShiftFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\WorkingShift\WorkingShiftRequest as Request;
use App\Models\Core\Auth\User;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\WorkingShift\WorkingShiftService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class WorkingShiftController extends Controller
{

    public function __construct(WorkingShiftService $service, WorkingShiftFilter $workingShiftFilter)
    {
        $this->service = $service;
        $this->filter = $workingShiftFilter;
    }

    public function index()
    {
        $paginated = $this->service
            ->filters($this->filter)
            ->with('details')
            ->withCount('attendances')
            ->latest('id')
            ->paginate(request()->get('per_page', 10));

        $paginated->each(function (WorkingShift $shift) {
            $details = $shift->details->firstWhere('is_weekend', 0);
            $shift->setAttribute('start_at', $details->start_at);
            $shift->setAttribute('end_at', $details->end_at);
            unset($shift->details);
            return $shift;
        });

        $response = $paginated->toArray();

        $response['data'] = $paginated->items();

        return $response;
    }

    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $this->service
                ->setAttributes($request->only('name', 'start_date', 'end_date', 'is_default', 'type', 'description'))
                ->checkIsDefault()
                ->save();

            $this->service
                ->setWorkingShiftDetails($request->details)
                ->saveDetails()
                ->when(count($request->get('departments')), function (WorkingShiftService $service) use ($request) {
                    //$service->assignToDepartments($request->get('departments'));
                    $service->assignToDepartmentAsUpcoming($request->get('departments'));
                })->assignToUserAsUpcoming($request->get('users'))
                //->assignToUsers($request->get('users'))
                ->notify('working_shift_created');

            return created_responses('work_shifts');
        });
    }


    public function show(WorkingShift $workingShift)
    {
        $workingShift
        ->load(['details',
            'departments' => fn($b) => $b
                ->whereDoesntHave('upcomingWorkingShift')
                ->select('departments.id', 'departments.name'),
            'users' => fn($b) => $b
                ->whereDoesntHave('upcomingWorkingShift')
                ->select('users.id', 'users.first_name', 'users.last_name'),
            'upcomingDepartments:id',
            'upcomingUsers:id']
        )->loadCount('attendances');

        $users = resolve(DepartmentRepository::class)
            ->employees($workingShift->departments->pluck('id')->toArray())
            ->pluck('id')
            ->toArray();

        $workingShift->setRelation(
            'users',
            $workingShift->users->filter(fn(User $user) => !in_array($user->id, $users))->values()
        );

        if ($workingShift->type === 'scheduled') {
            return $workingShift;
        }

        $details = $workingShift->details->firstWhere('is_weekend', 0);

        if ($details) {
            $workingShift->setAttribute('start_at', $details->start_at);
            $workingShift->setAttribute('end_at', $details->end_at);
        }

        return $workingShift;


    }

    public function update(WorkingShift $workingShift, Request $request)
    {
        DB::transaction(
            fn() => $this->service
                ->setIsUpdating(true)
                ->setAttributes($request->only('name', 'start_date', 'end_date', 'is_default', 'type', 'description'))
                ->setModel($workingShift)
                ->validateIfAttendanceNotExist('updated')
                ->update()
                ->setWorkingShiftDetails($request->get('details'))
                ->saveDetails()
                //->assignToDepartments($request->get('departments', []))
                ->assignUpdateToDepartmentAsUpcoming($request->get('departments', []))
                ->assignUpdateToUserAsUpcoming($request->get('users', []))
                //->assignToUsers($request->get('users', []))
                ->notify('working_shift_updated')
        );

        return updated_responses('work_shifts');
    }


    public function destroy(WorkingShift $workingShift)
    {
        if ($workingShift->is_default) {
            throw new GeneralException(__t('action_not_allowed'));
        }

        DB::transaction(
            fn() => $this->service
                ->setModel($workingShift)
                ->setIsUpdating(true)
                ->validateIfAttendanceNotExist('deleted')
                ->deleteDetails()
                ->endPreviousWorkingShiftOfUsers()
                ->endPreviousWorkingShiftsOfDepartments()
                ->deleteUpcomingUsersAndDepartment()
                ->delete()
                ->notify('working_shift_deleted')
    );

        return deleted_responses('work_shifts');
    }
}
