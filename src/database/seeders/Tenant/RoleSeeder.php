<?php

namespace Database\Seeders\Tenant;

use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!optional(tenant())->is_single) {
            return;
        }

        $manager = new Role([
            'name' => 'Manager',
            'type_id' => Type::findByAlias('tenant')->id,
            'is_default' => 1,
            'alias' => 'manager'
        ]);

        $manager->save();

        $manager->permissions()->sync(
            Permission::all(['id', 'name'])
                ->whereNotIn('name', ['access_own_departments'])
                ->pluck('id')->toArray()
        );

        $depart_manager = new Role([
            'name' => 'Department Manager',
            'type_id' => Type::findByAlias('tenant')->id,
            'is_default' => 1,
            'alias' => 'department_manager'
        ]);

        $depart_manager->save();

        $depart_manager->permissions()->sync(
            Permission::query()->whereIn('name', $this->departmentMangerRolePermissions())
                ->pluck('id')->toArray()
        );

        $employee = new Role([
            'name' => 'Employee',
            'type_id' => Type::findByAlias('tenant')->id,
            'is_default' => 1,
            'alias' => 'employee'
        ]);
        $employee->save();
        $employee->permissions()->sync(
            Permission::query()->whereIn('name', $this->employeeRolePermissions())
                ->pluck('id')->toArray()
        );
    }

    function departmentMangerRolePermissions(): array
    {
        return [
            //behavior
            'access_own_departments',

            //users
            'view_users',

            //department
            'view_departments',

            //employee-status
            'view_employment_statuses',

            //designations
            'view_designations',

            //leaves
            'view_all_leaves',
//            'update_employee_leave_amount',
            'view_leave_status',
            'view_leave_summaries',
            'view_leave_calendar',
            'view_leave_requests',
            'create_leave_request',
            'manage_cancel_leave',
            'update_leave_notes',
            'assign_leaves',
            'manage_approve_leave',
            'manage_reject_leave',
            'manage_bypass_leave',

            //attendance
            'view_all_attendance',
            'view_attendances_details',
            'attendances_daily_log',
            'view_attendance_summary',
            'view_attendance_requests',
            'send_attendance_request',
            'cancel_attendance',
            'update_attendance_notes',
            'create_attendances',
            'update_attendance_status',
            'update_attendances',
            'approve_attendance',
            'reject_attendance',

            //work-shift
            'view_working_shifts',
            'add_employees_to_working_shift',

            //holiday
            'view_all_departments_holidays',
            'view_holidays',

            //employee
            'view_employees',
            'update_employees',
            'delete_employees',
//            'invite_employees',
//            'add_employees',
            'terminate_employees',
            'rejoin_employees',
            'cancel_employee_invitation',
            'view_employee_address',
            'update_employee_address',
            'delete_employee_address',
            'view_employee_emergency_contacts',
            'create_employee_emergency_contacts',
            'update_employee_emergency_contacts',
            'delete_employee_emergency_contacts',
            'view_employee_social_links',
            'update_employee_social_links',
            'update_employees_profile',
            'update_employee_job_history',
            'view_job_history',

            //export
            'export_attendance_summery',
            'export_leave_summery',
            'export_attendance_daily_log',
            //import
            'import_attendances',
        ];
    }

    function employeeRolePermissions(): array
    {
        return [
            //leaves
            'view_leave_status',
            'view_leave_summaries',
            'view_leave_calendar',
            'view_leave_requests',
            'create_leave_request',
            'manage_cancel_leave',
            'update_leave_notes',

            //attendance
            'view_attendances_details',
            'attendances_daily_log',
            'view_attendance_summary',
            'view_attendance_requests',
            'send_attendance_request',
            'cancel_attendance',
            'update_attendance_notes',

            //holiday
            'view_holidays',

            //employee
            'view_employee_address',
            'update_employee_address',
            'delete_employee_address',
            'view_employee_emergency_contacts',
            'create_employee_emergency_contacts',
            'update_employee_emergency_contacts',
            'delete_employee_emergency_contacts',
            'view_employee_social_links',
            'update_employee_social_links',
            'update_employees_profile',
            'view_job_history',
            'view_salary',
            'view_payslip_pdf',
        ];
    }
}
