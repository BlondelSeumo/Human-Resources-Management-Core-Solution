<?php


namespace App\Http\Requests\Tenant\WorkingShift;


use App\Http\Requests\BaseRequest;

class WorkingShiftRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|min:2',
            'type' => 'required|in:regular,scheduled',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'details' => 'required|array',
            'start_at' => 'required_if:type,regular',
            'end_at' => 'required_if:type,regular',
            'weekdays' => 'required_if:type,regular',
            'details.*.end_at' => 'required_with:details.*.start_at',
            'details.*.weekday' => 'required|min:1',
        ];
    }

}
