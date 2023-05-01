<?php

namespace App\Http\Requests\Tenant\Attendance;

use App\Http\Requests\BaseRequest;

class AttendanceSettingsRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'alert_area' => ['required_if:punch_in_out_alert,true','array'],
            'punch_in_out_interval' => ['required_if:punch_in_out_alert,true','nullable','numeric', 'min:1'],
        ];
    }
}
