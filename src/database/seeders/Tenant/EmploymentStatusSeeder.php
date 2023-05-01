<?php


namespace Database\Seeders\Tenant;


use App\Models\Tenant\Employee\EmploymentStatus;
use App\Services\Tenant\Utility\AutoCreateService;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class EmploymentStatusSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

        EmploymentStatus::query()->truncate();

        resolve(AutoCreateService::class)
            ->createEmploymentStatus();

        $this->enableForeignKeys();

    }
}
