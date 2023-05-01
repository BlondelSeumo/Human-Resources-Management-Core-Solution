<?php


namespace App\Http\Controllers\Tenant\Employee;


use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Services\Core\Auth\UserService;
use App\Services\Tenant\Employee\EmploymentStatusService;
use App\Services\Tenant\WorkingShift\WorkingShiftService;
use Illuminate\Http\Request;

class EmployeeProfileController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function update(User $employee, Request $request)
    {
        $this->service
            ->validateIsNotDemoVersion()
            ->setModel($employee)
            ->validate();

        $employee->update($request->only('first_name', 'last_name', 'email'));

        $employee->profile()->updateOrCreate(
            ['user_id' => $employee->id],
            array_merge(
                ['user_id' => $employee->id],
                $request->only('employee_id', 'gender', 'date_of_birth', 'about_me', 'phone_number')
            )
        );

        return updated_responses('employee');
    }

    public function employeeId()
    {
        $count = User::count() + 1;

        return "EMP-$count";
    }

    public function updateEmployees($type)
    {
        $users_id = request()->get('users');
        if ($type == 'joining-date') {
            validator(\request()->all(), [
                'joining_date' => 'required|date',
             ])->validate();
            User::query()
                ->whereIn('id', $users_id)
                ->get()
                ->map(fn($employee) => $employee->profile()->update(['joining_date' => request()->get('joining_date')]));
            return updated_responses('employees');
        }
        else if ($type == 'workshift'){
            validator(\request()->all(), [
                'work_shift_id' => 'required',
            ])->validate();
            $workingShift = WorkingShift::query()->findOrFail(request()->get('work_shift_id'));
            resolve(WorkingShiftService::class)
                ->setModel($workingShift)
                ->assignToUsers($users_id);
            return updated_responses('employees');
        }
        else if ($type == 'terminate'){
            $terminateStatus = EmploymentStatus::getByAlias('terminated');
            resolve(EmploymentStatusService::class)
                ->setModel($terminateStatus)
                ->setAttributes(request()->only('description'))
                ->endPreviousEmploymentStatus($users_id)
                ->assignToUsers($users_id)
                ->changeEmployeeStatus($users_id, 'inactive');
            return [
                'status' => true,
                'message' => trans('default.status_updated_response', [
                    'name' => __t('employee'),
                    'status' => $terminateStatus->name
                ])
            ];
        }
        else if ($type == 'remove-employee'){
            User::query()
                ->whereIn('id', $users_id)
                ->update(['is_in_employee' => 0]);
            return response()->json([
                'status' => true,
                'message' => __t('user_removed_from_employee')
            ]);
        }
        else if ($type == 'rejoining'){
            validator(\request()->all(), [
                'employment_status_id' => 'required',
            ])->validate();
            $employmentStatus = EmploymentStatus::find(request()->get('employment_status_id'));
            resolve(EmploymentStatusService::class)
                ->setModel($employmentStatus)
                ->setAttributes(request()->only('description'))
                ->endPreviousEmploymentStatus($users_id)
                ->assignToUsers($users_id)
                ->changeEmployeeStatus($users_id, 'active');
            return [
                'status' => true,
                'message' => trans('default.status_updated_response', [
                    'name' => __t('employee'),
                    'status' => $employmentStatus->name
                ])
            ];
        }else{
            throw new GeneralException(__t('action_not_allowed'));
        }

    }


}
