<?php

namespace Database\Seeders\Tenant;

use App\Models\Core\Auth\Profile;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\DepartmentUser;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Services\Tenant\Employee\EmployeeService;
use Illuminate\Database\Seeder;

class UserJobDeskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::query()->find(1);

        resolve(EmployeeService::class)
            ->setModel($user)
            ->setAttrs([
                'designation_id' => Designation::query()->find(1)->id,
                'employment_status_id' => EmploymentStatus::query()->where('alias', 'permanent')->first()->id,
            ])->setIsInvite(true)
            ->assignToDesignation()
            ->assignEmploymentStatus();

        DepartmentUser::insert([
            'department_id' => Department::query()->find(1)->id,
            'user_id' => $user->id,
            'start_date' => now()->format('Y-m-d')
        ]);
        Profile::query()->create([
            'user_id' => $user->id,
            'employee_id' => 'EMP-1'
        ]);
    }
}
