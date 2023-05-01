<?php


namespace Database\Seeders\Tenant;


use App\Manager\Employee\EmployeeManager;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\WorkingShift\WorkingShift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    protected EmployeeManager $manager;

    public function __construct(EmployeeManager $manager)
    {
        $this->manager = $manager;
    }

    public function run()
    {
        $statusActive = Status::findByNameAndType('status_active', 'user')->id;
        User::query()->insert([
            [
                'first_name' => 'Steve',
                'last_name' => 'Rogers',
                'email' => 'manager@demo.com',
                'password' => Hash::make('123456'),
                'status_id' => $statusActive,
                'is_in_employee' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Tony',
                'last_name' => 'Stark',
                'email' => 'dept.manager@demo.com',
                'password' => Hash::make('123456'),
                'status_id' => $statusActive,
                'is_in_employee' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Dr.',
                'last_name' => 'Banner',
                'email' => 'employee@demo.com',
                'password' => Hash::make('123456'),
                'status_id' => $statusActive,
                'is_in_employee' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $roles = ['Manager', 'Department Manager', 'Employee'];
        User::query()->whereIn('id', [2, 3, 4])->each(function (User $user, $index) use ($roles) {
            $user->assignRole($roles[$index]);
            $this->manager->walkInto('designation', Designation::inRandomOrder()->first()->id)->assignEmployees($user->id);
            $this->manager->walkInto('workShift', WorkingShift::inRandomOrder()->first()->id)->assignEmployees($user->id);
            $this->manager->walkInto('employmentStatus', EmploymentStatus::query()->where('alias', 'permanent')->first()->id)->assignEmployees($user->id);
            $user->profile()->updateOrCreate([
                'user_id' => $user->id
            ], [
                'user_id' => $user->id,
                'employee_id' => Str::random(10)
            ]);
            $user->salary()->create([
                'user_id' => $user->id,
                'added_by' => 1,
                'amount' => collect([30000, 40000, 50000])->random(),
                'start_at' => now()->subtract('month', 2)
            ]);
        });
        $employeeRole = Role::query()->where('alias', 'employee')->first()->id;
        factory(User::class, 10)->create()->each(function (User $user) use ($employeeRole) {
            $user->assignRole($employeeRole);
            $this->manager->walkInto('designation', Designation::inRandomOrder()->first()->id)->assignEmployees($user->id);
            $this->manager->walkInto('workShift', WorkingShift::inRandomOrder()->first()->id)->assignEmployees($user->id);
            $this->manager->walkInto('employmentStatus', EmploymentStatus::inRandomOrder()->first()->id)->assignEmployees($user->id);
            $user->profile()->updateOrCreate([
                'user_id' => $user->id
            ], [
                'user_id' => $user->id,
                'employee_id' => Str::random(10)
            ]);
            $user->salary()->create([
                'user_id' => $user->id,
                'added_by' => 1,
                'amount' => collect([10000, 30000, 20000, 40000])->random(),
                'start_at' => now()->subtract('month', 6)
            ]);
        });
    }
}
