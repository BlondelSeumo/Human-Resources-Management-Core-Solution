<?php


namespace Database\Seeders\Tenant;


use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\Designation;
use App\Services\Tenant\Utility\AutoCreateService;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

        Designation::query()->truncate();

        /** @var Department $department */
        $department = Department::query()->first();

        resolve(AutoCreateService::class)
            ->createDesignation($department, [
                'tenant_id' => 1
            ]);

        $this->enableForeignKeys();

    }
}
