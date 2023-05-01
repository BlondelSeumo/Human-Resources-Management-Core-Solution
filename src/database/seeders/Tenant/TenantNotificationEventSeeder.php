<?php

namespace Database\Seeders\Tenant;

use App\Models\Core\Auth\Type;
use App\Models\Core\Setting\NotificationEvent;
use Illuminate\Database\Seeder;

class TenantNotificationEventSeeder extends Seeder
{
    public function run()
    {
        $tenant = Type::findByAlias('tenant')->id;

        $events = [
            [
                'name' => 'department_created',
                'type_id' => $tenant,
            ],
            [
                'name' => 'department_updated',
                'type_id' => $tenant,
            ],
            [
                'name' => 'department_deactivated',
                'type_id' => $tenant,
            ],
            [
                'name' => 'department_activated',
                'type_id' => $tenant,
            ],
            [
                'name' => 'working_shift_created',
                'type_id' => $tenant,
            ],
            [
                'name' => 'working_shift_updated',
                'type_id' => $tenant,
            ],
            [
                'name' => 'working_shift_deleted',
                'type_id' => $tenant,
            ],
            [
                'name' => 'employee_invitation',
                'type_id' => $tenant,
            ],
            [
                'name' => 'employee_invitation_canceled',
                'type_id' => $tenant,
            ],
            [
                'name' => 'employee_invited',
                'type_id' => $tenant,
            ],
            [
                'name' => 'employee_password_reset',
                'type_id' => $tenant,
            ],
//            [
//                'name' => 'employee_terminated',
//                'type_id' => $tenant,
//            ],
            [
                'name' => 'employee_termination',
                'type_id' => $tenant,
            ],
            [
                'name' => 'employee_salary_increment',
                'type_id' => $tenant,
            ],
            [
                'name' => 'salary_increment',
                'type_id' => $tenant,
            ],
            [
                'name' => 'employee_payslip_generate',
                'type_id' => $tenant,
            ],
            [
                'name' => 'attendance_requested',
                'type_id' => $tenant,
            ],
            [
                'name' => 'attendance_rejected',
                'type_id' => $tenant,
            ],
            [
                'name' => 'attendance_approved',
                'type_id' => $tenant,
            ],
            [
                'name' => 'attendance_canceled',
                'type_id' => $tenant,
            ],
            [
                'name' => 'leave_requested',
                'type_id' => $tenant,
            ],
            [
                'name' => 'leave_rejected',
                'type_id' => $tenant,
            ],
            [
                'name' => 'leave_approved',
                'type_id' => $tenant,
            ],
            [
                'name' => 'leave_canceled',
                'type_id' => $tenant,
            ],
            [
                'name' => 'leave_bypassed',
                'type_id' => $tenant,
            ],
            [
                'name' => 'leave_assigned',
                'type_id' => $tenant,
            ],
        ];

        NotificationEvent::query()
            ->insert($events);
    }
}
