<?php

namespace App\Http\Requests\Tenant\Employee;

use App\Http\Requests\BaseRequest;

class EmployeeStatusRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'description' => 'nullable'
        ];
    }
}
