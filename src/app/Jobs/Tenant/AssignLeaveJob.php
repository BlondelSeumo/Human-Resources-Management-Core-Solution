<?php

namespace App\Jobs\Tenant;

use App\Helpers\Traits\SettingHelper;
use App\Models\Tenant\Leave\UserLeave;
use App\Services\Tenant\Employee\EmployeeLeaveAllowanceService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignLeaveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels,
        SettingHelper;


    private EmployeeLeaveAllowanceService $service;

    public function __construct()
    {
        //
    }


    public function handle(EmployeeLeaveAllowanceService $service)
    {
        $this->service = $service;

        UserLeave::all()->map(function (UserLeave $userLeave){
            if (todayFromApp() > $userLeave->end_date){
                $this->service
                    ->setAttr('leave_type_id', $userLeave->leaveType->id)
                    ->setAttr('ranges', $this->leaveYear())
                    ->setEmployee($userLeave->user)
                    ->buildUserLeave()
                    ->checkAndSetMonthlyEarningAmount($userLeave->leaveType)
                    ->update();
            }
        });
    }
}
