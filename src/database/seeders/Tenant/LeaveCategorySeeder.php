<?php


namespace Database\Seeders\Tenant;


use App\Models\Tenant\Leave\LeaveType;
use App\Services\Tenant\Utility\AutoCreateService;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class LeaveCategorySeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

        LeaveType::query()->truncate();

        resolve(AutoCreateService::class)
            ->createLeaveCategory();

        $this->enableForeignKeys();

    }
}
