<?php


namespace Database\Seeders\Tenant;


use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Services\Tenant\Utility\AutoCreateService;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class WorkingShiftSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

        WorkingShift::query()->truncate();

        resolve(AutoCreateService::class)
            ->createWorkingShift();

        $this->enableForeignKeys();
    }
}
