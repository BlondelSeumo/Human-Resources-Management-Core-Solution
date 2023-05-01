<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Services\Tenant\Employee\EmployeeUpdateService;
use Illuminate\Support\Facades\DB;

class EmployeeUpdateController extends Controller
{
    public function __construct(EmployeeUpdateService $service)
    {
        $this->service = $service;
    }

    public function update(User $employee, $type){

        DB::transaction(function () use($employee, $type) {
            $this->service
                ->setMethod($type)
                ->validateMethod()
                ->checkPermissions()
                ->setModel($employee)
                ->setAttributes(request()->except('allowed_resource', 'tenant_id', 'tenant_short_name'))
                ->callMethod();
        });

        return updated_responses('employee');

    }
}
