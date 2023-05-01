<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Salary\Salary;
use Illuminate\Support\Facades\DB;

class EmployeeSalaryController extends Controller
{
    public function index(User $employee)
    {
        return Salary::query()
            ->where('user_id', $employee->id)
            ->with('addedBy')
            ->latest()
            ->get();
    }

    public function range()
    {
        $range = DB::table('salaries')
            ->selectRaw('MIN(amount) AS min_salary, MAX(amount) AS max_salary')
            ->first();

        return json_encode($range);
    }

}
