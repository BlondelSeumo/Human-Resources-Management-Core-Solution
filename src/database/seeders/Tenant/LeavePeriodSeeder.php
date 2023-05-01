<?php


namespace Database\Seeders\Tenant;


use App\Models\Tenant\Leave\LeavePeriod;
use App\Services\Tenant\Utility\AutoCreateService;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class LeavePeriodSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

        LeavePeriod::query()->truncate();

        resolve(AutoCreateService::class)
            ->createLeavePeriod();

        $this->enableForeignKeys();

    }
}
