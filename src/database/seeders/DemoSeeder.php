<?php

namespace Database\Seeders;

use Database\Seeders\Auth\UserTableSeeder;
use Database\Seeders\Employee\EmployeeContactSeeder;
use Database\Seeders\Tenant\AttendanceSeeder;
use Database\Seeders\Tenant\DefaultPayrunSeeder;
use Database\Seeders\Tenant\DesignationDemoSeeder;
use Database\Seeders\Tenant\HolidaySeeder;
use Database\Seeders\Tenant\LeaveSeeder;
use Database\Seeders\Tenant\LeaveStatusSeeder;
use Database\Seeders\Tenant\OrganizationSeeder;
use Database\Seeders\Tenant\PayrunSettingSeeder;
use Database\Seeders\Tenant\RequestAttendanceSeeder;
use Database\Seeders\Tenant\UserDepartmentSeeder;
use Database\Seeders\Tenant\UserLeaveSeeder;
use Database\Seeders\Tenant\UserSeeder;
use Database\Seeders\Tenant\WorkingShiftDemoSeeder;
use Database\Seeders\Tenant\WorkingShiftSeeder;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        activity()->withoutLogs(function () {
            $this->call(DatabaseSeeder::class);
            $this->call(WorkingShiftSeeder::class);
            $this->call(DesignationDemoSeeder::class);
            $this->call(UserSeeder::class);
            $this->call(OrganizationSeeder::class);
            $this->call(UserDepartmentSeeder::class);
            $this->call(EmployeeContactSeeder::class);
            $this->call(AttendanceSeeder::class);
            $this->call(RequestAttendanceSeeder::class);
            $this->call(UserLeaveSeeder::class);
            $this->call(LeaveSeeder::class);
            //$this->call(LeaveStatusSeeder::class);
            $this->call(PayrunSettingSeeder::class);
            $this->call(DefaultPayrunSeeder::class);
            $this->call(HolidaySeeder::class);
            $this->call(WorkingShiftDemoSeeder::class);
        });
    }
}
