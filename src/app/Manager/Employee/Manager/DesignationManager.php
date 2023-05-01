<?php


namespace App\Manager\Employee\Manager;


use App\Services\Tenant\Employee\DesignationService;

class DesignationManager extends BaseManager implements EmployeeManagerContract
{

    public function assignEmployees($employees)
    {
        $employees = is_array($employees) ? $employees : func_get_args();

        return resolve(DesignationService::class)
            ->setDesignationId($this->getModel())
            ->endPreviousDesignationOfUsers($employees)
            ->assignToUsers($employees);
    }
}