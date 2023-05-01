<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Services\Tenant\Leave\LeaveAssignService;
use Illuminate\Support\Facades\DB;

class LeaveAssignController extends Controller
{
    public function __construct(LeaveAssignService $service)
    {
        $this->service = $service;
    }

    public function store()
    {
        $this->service
            ->setAttributes(request()->all())
            ->basicValidations()
            ->setDurationType(request()->get('leave_duration'))
            ->validateDurationType()
            ->validateAttributes();

        $employee = User::query()->find(request()->get('employee_id'))->load('department');

        DB::transaction(function () use ($employee) {
            $this->service
                ->setModel($employee)
                ->checkPermissionsAndAssigner()
                ->when($this->service->durationType == 'hours',
                    fn(LeaveAssignService $service) => $service->validTimeWithWorkingShift(),
                    fn(LeaveAssignService $service) => $service->setWorkingShiftTime()
                )->userAvailableLeavesCheck()
//                ->validateLeaveYear()
                ->validateWithExistingLeaves()
                ->validateWithHolidays()
                ->assignLeave()
                ->sendNotification($this->service->leave);
        });

        return created_responses('leave');
    }
}
