<?php


namespace App\Services\Tenant\Leave;


use App\Models\Tenant\Leave\LeavePeriod;
use App\Services\Tenant\TenantService;

class LeavePeriodService extends TenantService
{
    public function __construct(LeavePeriod $leavePeriod)
    {
        $this->model = $leavePeriod;
    }

    public function validate()
    {
        validator($this->getAttributes(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ])->validate();

        return $this;
    }
}
