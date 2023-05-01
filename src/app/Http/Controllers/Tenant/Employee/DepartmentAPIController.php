<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Filters\Tenant\DepartmentFilter;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\Department;

class DepartmentAPIController extends Controller
{
    public function __construct(DepartmentFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index()
    {
        return Department::active()->filters($this->filter)
            ->get(['id', 'name']);
    }
}
