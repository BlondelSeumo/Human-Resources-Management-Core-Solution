<?php


namespace App\Manager\Employee\Manager;


use App\Services\Tenant\Employee\EmploymentStatusService;

class EmploymentStatusManager extends BaseManager implements EmployeeManagerContract
{
    public array $attributes = [];

    public function assignEmployees($employees)
    {
        $employees = is_array($employees) ? $employees : func_get_args();

        return resolve(EmploymentStatusService::class)
            ->setEmploymentStatusId($this->getModel())
            ->setAttributes($this->getAttributes())
            ->endPreviousEmploymentStatus($employees)
            ->assignToUsers($employees);
    }

    public function setAttributes(array $attributes): EmploymentStatusManager
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

}