<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\Department;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Employee\DepartmentService;

class DepartmentStatusController extends Controller
{
    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    public function update(Department $department)
    {
        throw_if(!$department->department_id, new GeneralException(__t('action_not_allowed')));

        $method = $department->isActive() ? 'departmentInactive' : 'departmentActive';

        $status_id = resolve(StatusRepository::class)->$method();

        $department->update([
            'status_id' => $status_id
        ]);

        $department = $department->load('status');

        $this->service
            ->setModel($department)
            ->notify($department->isActive() ? 'department_activated' : 'department_deactivated');

        return status_response(
            'department',
            $department->isActive() ? 'status_active' : 'status_inactive',
            [
                'data' => $department,
                'users' => $department->users
            ]
        );
    }

}


