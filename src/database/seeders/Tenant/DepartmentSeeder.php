<?php


namespace Database\Seeders\Tenant;


use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Services\Tenant\Utility\AutoCreateService;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    use DisableForeignKeys;

    public function run()
    {
        $this->disableForeignKeys();

        Department::query()->truncate();

        resolve(AutoCreateService::class)
            ->createDepartment([
                'tenant_id' => 1,
                'manager_id' => User::query()->first()->id
            ]);

        $this->enableForeignKeys();

    }
}
