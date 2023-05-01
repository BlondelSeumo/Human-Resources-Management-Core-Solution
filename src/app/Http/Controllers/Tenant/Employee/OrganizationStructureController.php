<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\Department;

class OrganizationStructureController extends Controller
{
    public function index()
    {
        return Department::with('manager:id,first_name,last_name,email', 'childDepartments:id,name,manager_id,department_id', 'childDepartments.manager:id,first_name,last_name,email')
            ->whereNull('department_id')
            ->first();
    }
}
