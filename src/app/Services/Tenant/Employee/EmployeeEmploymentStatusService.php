<?php

namespace App\Services\Tenant\Employee;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\UserEmploymentStatus;
use App\Services\Tenant\TenantService;

class EmployeeEmploymentStatusService extends TenantService
{

    protected EmploymentStatusService $employmentStatusService;

    public function __construct(EmploymentStatusService $employmentStatusService)
    {

        $this->employmentStatusService = $employmentStatusService;
    }

    public function changeStatus(User $employee, string $userStatus)
    {
        $this->employmentStatusService
            ->setModel($this->getModel())
            ->setAttributes($this->getAttributes('description'))
            ->endPreviousEmploymentStatus($employee->id)
            ->assignToUsers($employee->id);

        $employee->markAs($userStatus);

        return $employee;
    }

    public function updateNote(User $employee)
    {
        UserEmploymentStatus::where('user_id', $employee->id)
            ->where('employment_status_id', optional($employee->employmentStatus)->id)
            ->whereNull('end_date')
            ->update($this->getAttributes('description'));

        return $employee;
    }

    public function validateDescription()
    {
        validator($this->getAttributes(), [
            'description' => 'required'
        ])->validate();

        return $this;
    }


}
