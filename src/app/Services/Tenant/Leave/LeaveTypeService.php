<?php


namespace App\Services\Tenant\Leave;


use App\Exceptions\GeneralException;
use App\Models\Tenant\Leave\LeaveType;
use App\Services\Tenant\TenantService;

class LeaveTypeService extends TenantService
{
    public function __construct(LeaveType $leaveType)
    {
        $this->model = $leaveType;
    }

    public function validate()
    {
        validator($this->getAttributes(), [
            'name' => 'required|min:2',
            'type' => 'required|in:paid,unpaid,special',
            'amount' => 'required',
            'special_percentage' => 'required_if:type,special',
            'is_enabled' => 'required',
            'is_earning_enabled' => 'required',
        ])->validate();

        return $this;
    }

    public function validateLeaves(): LeaveTypeService
    {
        $this->model->fill($this->getAttributes());

        throw_if(
            $this->model->isDirty('type') && $this->model->hasLeave(),
            new GeneralException(__t('you_cant_update_leave_type_if_the_type_already_has_leave_applied'))
        );

        return $this;
    }
}
