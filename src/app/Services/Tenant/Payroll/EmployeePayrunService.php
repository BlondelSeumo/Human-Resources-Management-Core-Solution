<?php

namespace App\Services\Tenant\Payroll;

use App\Exceptions\GeneralException;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Payroll\PayrunType;
use App\Services\Tenant\TenantService;

class EmployeePayrunService extends TenantService
{
    public function __construct(User $employee)
    {
        $this->model = $employee;
    }

    public function validateUserException($except, $restriction)
    {
        if (array_key_exists('eligible_audience', $except) && $except['eligible_audience'] == 'all') {
            return $this;
        }
        $user_except = array_key_exists('users', $except) ? json_decode($except['users']) : [];
        $department_except = array_key_exists('departments', $except) ? json_decode($except['departments']) : [];
        $status_except = array_key_exists('employment_statuses', $except) ? json_decode($except['employment_statuses']) : [];

        if (in_array($this->model->id, $user_except) ||
            ($this->model->department->first() && in_array($this->model->department->first()->id, $department_except)) ||
            ($this->model->employmentStatus->first() && in_array($this->model->employmentStatus->first()->id, $status_except))
        ) {
            $this->model[$restriction] = true;
        };
        return $this;

    }

    public function setDefaultSettingRelation($except)
    {
        $default_payrun = PayrunType::query()
            ->where('is_default', 1)
            ->with([
                'setting',
                'beneficiaries',
                'beneficiaries.beneficiary',
            ])
            ->first();

        $this->model->setRelation('default_payrun', $default_payrun);

        return $this;
    }

    public function validateForEmployeesPayslipsAccess($access): self
    {
        throw_if(
            $access == 'no' && $this->model->id != auth()->id(),
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

}
