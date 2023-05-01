<?php

namespace App\Jobs\Tenant;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\WorkingShift\UpcomingDepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\UpcomingUserWorkingShift;
use App\Services\Tenant\Employee\DepartmentService;
use App\Services\Tenant\Employee\EmployeeService;
use App\Services\Tenant\WorkingShift\WorkingShiftService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AssignUpcomingWorkingShiftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        UpcomingDepartmentWorkingShift::query()
            ->where(function ($q){
                $q->whereDate('start_date', todayFromApp())
                    ->whereDate('created_at', '<' , todayFromApp());
            })->orWhereDate('start_date', '<', todayFromApp())
            ->get()->each(function ($data){
                resolve(DepartmentService::class)
                    ->setModel(Department::query()->find($data->department_id))
                    ->setIsUpdate(true)
                    ->setAttr('working_shift_id', $data->working_shift_id)
                    ->assignWorkingShift();

                UpcomingDepartmentWorkingShift::query()->find($data->id)->delete();
            });

        UpcomingUserWorkingShift::query()
            ->where(function ($q){
                $q->whereDate('start_date', todayFromApp())
                    ->whereDate('created_at', '<' , todayFromApp());
            })->orWhereDate('start_date', '<', todayFromApp())
            ->get()->each(function ($data){
                resolve(WorkingShiftService::class)
                    ->setWorkShiftId($data->working_shift_id)
                    ->assignToUsers([$data->user_id]);

                UpcomingUserWorkingShift::query()->find($data->id)->delete();
            });
    }
}
