<?php

namespace App\Http\Requests\Tenant\Employee;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class EmploymentStatusRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => '|unique:employment_statuses,name',
            'class' => 'required|in:purple,success,info,warning,primary,danger'
        ];
    }
}
