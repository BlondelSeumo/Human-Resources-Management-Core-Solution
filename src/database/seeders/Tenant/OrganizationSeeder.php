<?php

namespace Database\Seeders\Tenant;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run()
    {
        $dept_managers = User::query()
            ->whereHas('roles', fn (Builder $builder) => $builder->where('alias', 'employee'))
            ->whereHas('employmentStatus', fn (Builder $builder) => $builder->where('alias','!=','terminated'))
            ->where('email', '!=', 'employee@demo.com')
            ->inRandomOrder()
            ->get()
            ->take(4);
        $dept_managers->each(function (User $user){
            $user->assignRole('Department Manager');
        });
        $dept_manager = User::query()->where('email', 'dept.manager@demo.com')->first();

        $activeStatus = resolve(StatusRepository::class)->departmentActive();
        Department::query()->insert([
            [
                'name' => 'Admin & HRM',
                'status_id' => $activeStatus,
                'department_id' => 1,
                'manager_id' => $dept_manager->id,
                'tenant_id' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Accounts',
                'status_id' => $activeStatus,
                'department_id' => 2,
                'manager_id' => $dept_managers[0]->id,
                'tenant_id' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Development',
                'status_id' => $activeStatus,
                'department_id' => 1,
                'manager_id' => $dept_managers[1]->id,
                'tenant_id' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Software',
                'status_id' => $activeStatus,
                'department_id' => 4,
                'manager_id' => $dept_managers[2]->id,
                'tenant_id' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'UI & UX',
                'status_id' => $activeStatus,
                'department_id' => 4,
                'manager_id' => $dept_managers[3]->id,
                'tenant_id' => 1,
                'created_at' => now(),
            ],
        ]);
//        factory(Department::class, 4)->create()->each(function (Department $department, $index) use ($dept_managers) {
//            $department->update([
//                'department_id' => optional(Department::whereNotNull('department_id')->inRandomOrder()->first())->id ?: 1,
//                'manager_id' => $dept_managers[$index]->id,
//            ]);
//        });
    }
}
