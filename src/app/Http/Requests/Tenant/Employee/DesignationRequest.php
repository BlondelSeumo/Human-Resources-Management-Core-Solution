<?php

namespace App\Http\Requests\Tenant\Employee;


use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Employee\Designation;

class DesignationRequest extends BaseRequest
{
    public function rules()
    {
        return $this->initRules( new Designation());
    }
}