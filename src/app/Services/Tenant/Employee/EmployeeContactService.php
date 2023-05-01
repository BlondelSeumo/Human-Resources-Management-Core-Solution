<?php

namespace App\Services\Tenant\Employee;

use App\Models\Core\Auth\User;
use App\Services\Tenant\TenantService;

class EmployeeContactService extends TenantService
{
    public function __construct(User $employee)
    {
        $this->model = $employee;
    }

    public function validateAddress()
    {
        validator($this->getAttributes(), [
            'details' => 'required|min:3',
            'type' => 'required|in:present_address,permanent_address'
        ])->validate();

        return $this;
    }

    public function updateAddress()
    {
        $this->model->addresses()->updateOrCreate([
            'user_id' => $this->model->id,
            'key' => $this->getAttribute('type')
        ], [
            'user_id' => $this->model->id,
            'key' => $this->getAttribute('type'),
            'value' => json_encode($this->getAttributes('details', 'area', 'city', 'state', 'zip_code', 'country', 'phone_number'))
        ]);

        return $this;
    }


    public function updateContact()
    {
        $this->model->contacts()->updateOrCreate([
            ''
        ], [

        ]);
    }
}
