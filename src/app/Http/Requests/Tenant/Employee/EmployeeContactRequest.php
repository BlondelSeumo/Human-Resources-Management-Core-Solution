<?php

namespace App\Http\Requests\Tenant\Employee;

use App\Http\Requests\BaseRequest;

class EmployeeContactRequest extends BaseRequest
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
            'phone_number' => 'required'
        ];
    }
}
