<?php


namespace Database\Seeders\App\Traits;


trait Permissions
{
    public function permissions($tenant, $app): array
    {
        return [
            [
                'name' => 'create_departments',
                'type_id' => $tenant,
                'group_name' => 'department'
            ],
            [
                'name' => 'view_departments',
                'type_id' => $tenant,
                'group_name' => 'department'
            ],
            [
                'name' => 'update_departments',
                'type_id' => $tenant,
                'group_name' => 'department'
            ],
            [
                'name' => 'delete_departments',
                'type_id' => $tenant,
                'group_name' => 'department'
            ],
            [
                'name' => 'update_departments_status',
                'type_id' => $tenant,
                'group_name' => 'department'
            ],
            [
                'name' => 'view_organization_chart',
                'type_id' => $tenant,
                'group_name' => 'department'
            ],
            [
                'name' => 'move_department_employees',
                'type_id' => $tenant,
                'group_name' => 'department'
            ],
            [
                'name' => 'view_department_user',
                'type_id' => $tenant,
                'group_name' => 'department'
            ],
            [
                'name' => 'create_employment_statuses',
                'type_id' => $tenant,
                'group_name' => 'employment_status'
            ],
            [
                'name' => 'view_employment_statuses',
                'type_id' => $tenant,
                'group_name' => 'employment_status'
            ],
            [
                'name' => 'update_employment_statuses',
                'type_id' => $tenant,
                'group_name' => 'employment_status'
            ],
            [
                'name' => 'delete_employment_statuses',
                'type_id' => $tenant,
                'group_name' => 'employment_status'
            ],
            [
                'name' => 'create_designations',
                'type_id' => $tenant,
                'group_name' => 'designation'
            ],
            [
                'name' => 'view_designations',
                'type_id' => $tenant,
                'group_name' => 'designation'
            ],
            [
                'name' => 'update_designations',
                'type_id' => $tenant,
                'group_name' => 'designation'
            ],
            [
                'name' => 'delete_designations',
                'type_id' => $tenant,
                'group_name' => 'designation'
            ],

            //Working Shift
            [
                'name' => 'create_working_shifts',
                'type_id' => $tenant,
                'group_name' => 'working_shift'
            ],
            [
                'name' => 'view_working_shifts',
                'type_id' => $tenant,
                'group_name' => 'working_shift'
            ],
            [
                'name' => 'update_working_shifts',
                'type_id' => $tenant,
                'group_name' => 'working_shift'
            ],
            [
                'name' => 'delete_working_shifts',
                'type_id' => $tenant,
                'group_name' => 'working_shift'
            ],
            [
                'name' => 'add_employees_to_working_shift',
                'type_id' => $tenant,
                'group_name' => 'working_shift'
            ],
            [
                'name' => 'view_all_departments_holidays',
                'type_id' => $tenant,
                'group_name' => 'holidays'
            ],
            [
                'name' => 'view_holidays',
                'type_id' => $tenant,
                'group_name' => 'holidays'
            ],
            [
                'name' => 'create_holidays',
                'type_id' => $tenant,
                'group_name' => 'holidays'
            ],
            [
                'name' => 'update_holidays',
                'type_id' => $tenant,
                'group_name' => 'holidays'
            ],
            [
                'name' => 'delete_holidays',
                'type_id' => $tenant,
                'group_name' => 'holidays'
            ],
            [
                'name' => 'view_employees',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'update_employees',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'delete_employees',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'invite_employees',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'add_employees',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'terminate_employees',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'rejoin_employees',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'add_user_to_employees',
                'type_id' => $tenant,
                'group_name' => 'users'
            ],
            [
                'name' => 'remove_user_from_employees',
                'type_id' => $tenant,
                'group_name' => 'users'
            ],
            [
                'name' => 'cancel_employee_invitation',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'view_employee_address',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'update_employee_address',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'delete_employee_address',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'view_employee_emergency_contacts',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'create_employee_emergency_contacts',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'update_employee_emergency_contacts',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'delete_employee_emergency_contacts',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'view_employee_social_links',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'update_employee_social_links',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'update_employees_profile',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'view_job_history',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'update_employee_job_history',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'view_salary',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'update_salary',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'update_employee_beneficiary',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],
            [
                'name' => 'update_employee_payrun',
                'type_id' => $tenant,
                'group_name' => 'employees'
            ],

            //Leaves
            [
                'name' => 'view_all_leaves',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'view_leave_types',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'create_leave_types',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'update_leave_types',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'delete_leave_types',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'view_leave_settings',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'update_leave_settings',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'manage_leave_allowance_policy',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'update_employee_leave_amount',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'view_leave_status',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'view_leave_summaries',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'view_leave_calendar',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'view_leave_requests',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'create_leave_request',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'manage_cancel_leave',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'update_leave_notes',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'assign_leaves',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'manage_approve_leave',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'manage_reject_leave',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],
            [
                'name' => 'manage_bypass_leave',
                'type_id' => $tenant,
                'group_name' => 'leave_label'
            ],

            //attendance
            [
                'name' => 'view_all_attendance',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'view_attendance_settings',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'update_attendance_settings',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'view_attendances_details',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'attendances_daily_log',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'view_attendance_summary',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'view_attendance_requests',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'send_attendance_request',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'cancel_attendance',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'update_attendance_notes',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'create_attendances',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'update_attendance_status',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'update_attendances',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'approve_attendance',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'reject_attendance',
                'type_id' => $tenant,
                'group_name' => 'attendance'
            ],
            [
                'name' => 'access_own_departments',
                'type_id' => $tenant,
                'group_name' => 'access_department'
            ],
            [
                'name' => 'access_all_departments',
                'type_id' => $tenant,
                'group_name' => 'access_department'
            ],

            //Payrun
            [
                'name' => 'view_payruns',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'update_payruns',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'delete_payruns',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'run_default_payrun',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'run_manual_payrun',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'send_payrun_payslips',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],

            //Payslip
            [
                'name' => 'view_payslips',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'view_payslip_pdf',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'manage_payslip_confliction',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'send_individual_payslip',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'send_bulk_payslip',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'edit_payslip',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'update_payslip',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'delete_payslip',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],

            //payroll summery
            [
                'name' => 'view_payroll_summery',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],

            //payroll beneficiary
            [
                'name' => 'view_beneficiaries',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'create_beneficiaries',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'edit_beneficiaries',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'update_beneficiaries',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'delete_beneficiaries',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],

            //payroll setting
            [
                'name' => 'view_payroll_settings',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'update_payrun_period',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'update_payrun_beneficiary',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],
            [
                'name' => 'update_payrun_audience',
                'type_id' => $tenant,
                'group_name' => 'payroll'
            ],

            //Import
            [
                'name' => 'import_employees',
                'type_id' => $tenant,
                'group_name' => 'import'
            ],
            [
                'name' => 'import_attendances',
                'type_id' => $tenant,
                'group_name' => 'import'
            ],

            //Export
            [
                'name' => 'export_attendance_summery',
                'type_id' => $tenant,
                'group_name' => 'export'
            ],
            [
                'name' => 'export_leave_summery',
                'type_id' => $tenant,
                'group_name' => 'export'
            ],
            [
                'name' => 'export_attendance_daily_log',
                'type_id' => $tenant,
                'group_name' => 'export'
            ],
            [
                'name' => 'export_assets',
                'type_id' => $tenant,
                'group_name' => 'export'
            ],
            [
                'name' => 'export_payslips',
                'type_id' => $tenant,
                'group_name' => 'export'
            ],

            //Announcement
            [
                'name' => 'create_announcements',
                'type_id' => $tenant,
                'group_name' => 'announcement'
            ],
            [
                'name' => 'view_announcements',
                'type_id' => $tenant,
                'group_name' => 'announcement'
            ],
            [
                'name' => 'update_announcements',
                'type_id' => $tenant,
                'group_name' => 'announcement'
            ],
            [
                'name' => 'delete_announcements',
                'type_id' => $tenant,
                'group_name' => 'announcement'
            ],
            //CompanyAssets
            [
                'name' => 'create_company_assets',
                'type_id' => $tenant,
                'group_name' => 'company_asset'
            ],
            [
                'name' => 'view_company_assets',
                'type_id' => $tenant,
                'group_name' => 'company_asset'
            ],
            [
                'name' => 'update_company_assets',
                'type_id' => $tenant,
                'group_name' => 'company_asset'
            ],
            [
                'name' => 'delete_company_assets',
                'type_id' => $tenant,
                'group_name' => 'company_asset'
            ],
            [
                'name' => 'create_company_asset_types',
                'type_id' => $tenant,
                'group_name' => 'company_asset'
            ],
            [
                'name' => 'view_company_asset_types',
                'type_id' => $tenant,
                'group_name' => 'company_asset'
            ],
            [
                'name' => 'update_company_asset_types',
                'type_id' => $tenant,
                'group_name' => 'company_asset'
            ],
            [
                'name' => 'delete_company_asset_types',
                'type_id' => $tenant,
                'group_name' => 'company_asset'
            ],
        ];
    }
}
