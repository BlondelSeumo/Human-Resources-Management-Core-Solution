<?php

namespace App\Jobs\Tenant;

use App\Helpers\Traits\SettingHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\Leave\LeaveType;
use App\Models\Tenant\Leave\UserLeave;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Tenant\Employee\EmployeeLeaveAllowanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignLeaveByStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,
        SettingKeyHelper, SettingHelper;


    private EmployeeLeaveAllowanceService $service;


    public function __construct()
    {
        //
    }


    public function handle(EmployeeLeaveAllowanceService $service)
    {
        $this->service = $service;

        $statuses = resolve(SettingRepository::class)->settings('leave')
            ->whereIn('name', ['statuses_for_paid_leave', 'statuses_for_unpaid_leave'])
            ->pluck('value', 'name')->toArray();

        $employmentStatuses = EmploymentStatus::query()
            ->with(['employees' => fn(BelongsToMany $many) => $many->wherePivotNull('end_date')])
            ->get();
        $paidStatuses = $employmentStatuses->whereIn('id', json_decode($statuses['statuses_for_paid_leave']));
        $unpaidStatuses = $employmentStatuses->whereIn('id', json_decode($statuses['statuses_for_unpaid_leave']));
        $noLeaveStatuses = $employmentStatuses->whereNotIn('id', array_unique(
                array_merge(
                    json_decode($statuses['statuses_for_paid_leave']),
                    json_decode($statuses['statuses_for_unpaid_leave'])
                )
            )
        );


        $leaveTypes = LeaveType::all();
        $paidLeaveTypes = $leaveTypes->where('type', 'paid')->where('is_enabled', 1);
        $unpaidLeaveTypes = $leaveTypes->where('type', 'unpaid')->where('is_enabled', 1);

        $paidStatuses->map(function (EmploymentStatus $status) use ($paidLeaveTypes, $unpaidLeaveTypes, $unpaidStatuses) {
            $status->employees->map(function (User $employee) use ($paidLeaveTypes, $unpaidLeaveTypes, $unpaidStatuses) {
                $paidLeaveTypes->map(function (LeaveType $leaveType) use ($employee) {
                    $this->service
                        ->setAttr('leave_type_id', $leaveType->id)
                        ->setAttr('ranges', $this->leaveYear())
                        ->setEmployee($employee)
                        ->buildUserLeave()
                        ->checkAndSetMonthlyEarningAmount($leaveType)
                        ->update();
                });
                if (!in_array($employee->employmentStatus->first()->id, $unpaidStatuses->pluck('id')->toArray())) {
                    $employee->assignedLeaves()->whereIn('leave_type_id', $unpaidLeaveTypes->pluck('id')->toArray())->delete();
                }
            });
        });

        $unpaidStatuses->map(function (EmploymentStatus $status) use ($unpaidLeaveTypes, $paidLeaveTypes, $paidStatuses) {
            $status->employees->map(function (User $employee) use ($unpaidLeaveTypes, $paidLeaveTypes, $paidStatuses) {
                $unpaidLeaveTypes->map(function (LeaveType $leaveType) use ($employee) {
                    $this->service
                        ->setAttr('leave_type_id', $leaveType->id)
                        ->setAttr('ranges', $this->leaveYear())
                        ->setEmployee($employee)
                        ->buildUserLeave()
                        ->checkAndSetMonthlyEarningAmount($leaveType)
                        ->update();
                });
                if (!in_array($employee->employmentStatus->first()->id, $paidStatuses->pluck('id')->toArray())) {
                    $employee->assignedLeaves()->whereIn('leave_type_id', $paidLeaveTypes->pluck('id')->toArray())->delete();
                }
            });
        });

        $noLeaveStatuses->map(function (EmploymentStatus $status) {
            $statusEmployeeIds = $status->employees->pluck('id')->toArray();
            UserLeave::query()->whereIn('user_id', $statusEmployeeIds)->delete();
        });

    }
}
