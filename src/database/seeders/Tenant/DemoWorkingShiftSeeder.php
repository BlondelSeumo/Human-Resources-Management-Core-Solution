<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Services\Tenant\Utility\AutoCreateService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoWorkingShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(WorkingShift::class, 10)->create()->each(function (WorkingShift $workingShift) {
            $start_at = now()->addHours(rand(2, 10));
            $end_at = Carbon::parse($start_at)->addHours(9);

            resolve(AutoCreateService::class)->saveWorkingShiftDetails($workingShift, $start_at->format('H:i:s'), $end_at->format('H:i:s'));
        });
    }
}
