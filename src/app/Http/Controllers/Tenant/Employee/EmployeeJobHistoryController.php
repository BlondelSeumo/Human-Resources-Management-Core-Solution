<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;

class EmployeeJobHistoryController extends Controller
{
    public function index(User $employee): User
    {
        return $employee
            ->load([
                'departments' => fn($b) => $b->orderBy('pivot_start_date', 'DESC')->select('id','name'),
                'workingShifts' => fn($b) => $b->orderBy('pivot_start_date', 'DESC')->select('id','name'),
                'designations' => fn($b) => $b->orderBy('pivot_start_date', 'DESC')->select('id','name'),
                'employmentStatuses' => fn($b) => $b->orderBy('pivot_start_date', 'DESC')->select('id','name'),
                'roles:id,name,alias',
                'upcomingWorkingShift',
                'upcomingWorkingShift.workingShift:id,name',
                'profile:id,user_id,joining_date'
            ]);
    }
}
