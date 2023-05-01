<?php

namespace Database\Seeders\Tenant;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\DepartmentUser;
use Illuminate\Database\Seeder;

class UserDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userDepartments = User::where('id', '!=', 1)
            ->get()
            ->map(function (User $user) {
                return [
                    'start_date' => now()->subYears(2),
                    'end_date' => null,
                    'department_id' => Department::inRandomOrder()->first()->id,
                    'user_id' => $user->id
                ];
            })->toArray();

        DepartmentUser::insert($userDepartments);
    }
}
