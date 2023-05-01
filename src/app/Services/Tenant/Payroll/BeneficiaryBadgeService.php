<?php

namespace App\Services\Tenant\Payroll;

use App\Exceptions\GeneralException;
use App\Models\Tenant\Payroll\Beneficiary;
use App\Services\Tenant\TenantService;

class BeneficiaryBadgeService extends TenantService
{
    public function __construct(Beneficiary $beneficiary)
    {
        $this->model = $beneficiary;
    }

    public function validate(): self
    {
        validator($this->getAttributes(), [
            'name' => 'required',
            'type' => 'required',
        ])->validate();
        return $this;
    }

    public function validateConstrain(): self
    {
        throw_if($this->model->values->count() && $this->model->is_active != $this->getAttr('is_active'),
            new GeneralException(__t('can_not_deactivate_used_badge'))
        );

        return $this;
    }

}
