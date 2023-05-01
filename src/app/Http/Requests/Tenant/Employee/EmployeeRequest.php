<?php

namespace App\Http\Requests\Tenant\Employee;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends BaseRequest
{

    public function rules()
    {
        $employee = $this->route()->parameter('employee');

        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore(optional($employee)->id)
            ],
            'employee_id' => 'required|min:2|unique:profiles,employee_id,'.optional($employee)->id.',user_id',
            'department_id' => 'required|integer',
            'designation_id' => 'required|integer',
            'employment_status_id' => 'required|integer',
            'work_shift_id' => 'nullable|integer',
            'gender' => 'required'
        ];
    }
}
