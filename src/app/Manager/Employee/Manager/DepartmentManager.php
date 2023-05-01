<?php


namespace App\Manager\Employee\Manager;


use App\Services\Tenant\Employee\DepartmentEmployeeService;

class DepartmentManager extends BaseManager implements EmployeeManagerContract
{

    public function assignEmployees($employees)
    {
        $employees = is_array($employees) ? $employees : func_get_args();

        return resolve(DepartmentEmployeeService::class)
            ->setDepartmentId($this->getModel())
            ->setAttributes(['users' => $employees])
            ->moveEmployee();
    }
}