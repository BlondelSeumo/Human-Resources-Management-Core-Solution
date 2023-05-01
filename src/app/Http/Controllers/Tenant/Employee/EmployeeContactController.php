<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\EmployeeContactRequest;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\UserContact;
use App\Services\Tenant\Employee\EmployeeContactService;

class EmployeeContactController extends Controller
{
    public function __construct(EmployeeContactService $service)
    {
        $this->service = $service;
    }

    public function index(User $employee)
    {
        return $employee->contacts->sortBy(function (UserContact $contact) {
            return $contact->id;
        });
    }

    public function store(User $employee, EmployeeContactRequest $request)
    {
        $employee->contacts()->save(new UserContact([
            'key' => 'emergency_contacts',
            'value' => json_encode($request->only('name', 'relationship', 'phone_number', 'email', 'details', 'city', 'country'))
        ]));

        return created_responses('emergency_contacts');
    }

    public function show(User $employee, UserContact $emergency_contact)
    {
        return $emergency_contact;
    }
    
    public function update(User $employee,UserContact $emergency_contact, EmployeeContactRequest $request)
    {
        $emergency_contact->update([
            'value' => $request->only('name', 'relationship', 'phone_number', 'email', 'details', 'city', 'country')
        ]);

        return updated_responses('emergency_contacts');
    }

    public function destroy(User $employee, UserContact $emergency_contact)
    {

        $emergency_contact->delete();


        return deleted_responses('emergency_contacts');
    }
}
