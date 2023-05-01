<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdditionalAccessBehavior
{
    public function handle(Request $request, Closure $next, $type)
    {
        $permissions = [
            'role' => ['attach_users_to_roles'],
            'work_shift' => ['create_working_shifts', 'update_working_shifts'],
            'holiday' => ['create_holidays', 'update_holidays'],
            'leave' => ['update_leave_settings'],
            'attendance' => ['update_attendance_settings'],
            'department' => ['create_departments', 'update_departments'],
            'payrun' => ['view_payroll_settings', 'run_manual_payrun']
        ];

        $behavior = 'own_departments';

        if (authorize_any($permissions[$type])){
            $behavior = 'all_departments';
        }

        $request->merge([
            'access_behavior' => $behavior
        ]);

        return $next($request);
    }
}
