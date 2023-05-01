<?php


namespace App\Manager\Employee\Manager;


use App\Services\Tenant\WorkingShift\WorkingShiftService;

class WorkShiftManager extends BaseManager implements EmployeeManagerContract
{

    public function assignEmployees($employees)
    {
        $employees = is_array($employees) ? $employees : func_get_args();

        return resolve(WorkingShiftService::class)
            ->setWorkShiftId($this->getModel())
            ->assignToUsers($employees);
    }
}