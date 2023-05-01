<?php

namespace App\Jobs\Tenant;

use App\Models\Tenant\WorkingShift\DepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateWorkingShiftJob implements ShouldQueue
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
     */
    public function handle()
    {
        $endWorkingShifts = WorkingShift::query()
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<', todayFromApp())
            ->pluck('id')
            ->toArray();

        WorkingShiftUser::query()
            ->whereIn('working_shift_id', $endWorkingShifts)
            ->whereNull('end_date')
            ->update([
                'end_date' => todayFromApp()
            ]);

        DepartmentWorkingShift::query()
            ->whereIn('working_shift_id', $endWorkingShifts)
            ->whereNull('end_date')
            ->update([
                'end_date' => todayFromApp()
            ]);
    }
}
