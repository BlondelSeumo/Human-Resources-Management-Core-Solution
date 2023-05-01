<?php

namespace App\Http\Requests\Tenant\Employee;

use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Leave\LeaveType;

class EmployeeLeaveRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0|max:365',
            'leave_type_id' => 'required|exists:'.LeaveType::class.',id',
        ];
    }
}
