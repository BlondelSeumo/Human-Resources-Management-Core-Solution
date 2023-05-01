<?php

namespace App\Http\Requests\Tenant\Employee;

use App\Http\Requests\BaseRequest;

class DepartmentRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:2',
            'manager_id' => 'required|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'working_shift_id' => 'required|exists:working_shifts,id',
        ];
    }
}
