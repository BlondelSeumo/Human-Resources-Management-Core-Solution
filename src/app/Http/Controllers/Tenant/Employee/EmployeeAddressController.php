<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Services\Tenant\Employee\EmployeeContactService;
use Illuminate\Http\Request;

class EmployeeAddressController extends Controller
{
    public function __construct(EmployeeContactService $service)
    {
        $this->service = $service;
    }

    public function show(User $employee)
    {
        return $employee->addresses;
    }

    public function update(User $employee, Request $request)
    {
        $this->service
            ->setAttributes($request->only('details', 'area', 'city', 'type', 'state', 'zip_code', 'country', 'phone_number'))
            ->validateAddress()
            ->setModel($employee)
            ->updateAddress();

        return updated_responses('address');
    }

    public function delete(User $employee, $type)
    {
        $employee->addresses()->where('key', $type)->delete();

        return deleted_responses('address');
    }
}
