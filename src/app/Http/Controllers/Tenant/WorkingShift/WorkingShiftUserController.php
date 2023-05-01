<?php

namespace App\Http\Controllers\Tenant\WorkingShift;

use App\Http\Controllers\Controller;
use App\Models\Tenant\WorkingShift\UpcomingUserWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\WorkingShift\WorkingShiftService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class WorkingShiftUserController extends Controller
{
    public function __construct(WorkingShiftService $service)
    {
        $this->service = $service;
    }

    public function index(WorkingShift $workingShift): array
    {
        $DeptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers(auth()->id());

        $workingShift = $workingShift->load(['users' => function ($builder) use($DeptUsers){
            $builder->when(request('access_behavior') == 'own_departments',
                    function (Builder $builder) use($DeptUsers){
                        $builder->whereIn('id', $DeptUsers)
                            ->whereDoesntHave('upcomingWorkingShift');
                    })->select('id');
        }]);

        $upcomingUser = UpcomingUserWorkingShift::query()
            ->when(request('access_behavior') == 'own_departments',
                function (Builder $builder) use($DeptUsers){
                    $builder->whereIn('id', $DeptUsers);
            })->where('working_shift_id', $workingShift->id)
            ->pluck('user_id')
            ->toArray();

        return array_merge($upcomingUser, $workingShift->users->pluck('id')->toArray());
    }

    public function store(WorkingShift $workingShift, Request $request)
    {
        $this->service
            ->setIsUpdating(true)
            ->setAttributes($request->get('users'))
            ->setModel($workingShift)
            ->validateUsers()
            ->assignUpdateToUserAsUpcoming(request('access_behavior') == 'own_departments' ?
                $this->service->mergeNonAccessibleUsers($workingShift, $request->get('users', [])) :
                request()->get('users', []));
//            ->assignToUsers( request('access_behavior') == 'own_departments' ?
//                $this->service->mergeNonAccessibleUsers($request->get('users', [])) :
//                request()->get('users', [])
//            );

        return [
            'status' => true,
            'message' => trans('default.added_response', [
                'subject' => trans('default.employees'),
                'object' => trans('default.work_shift')
            ])
        ];
    }
}
