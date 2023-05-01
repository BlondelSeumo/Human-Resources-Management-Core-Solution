<?php

namespace App\Http\Requests\Tenant\Employee;


use App\Http\Requests\BaseRequest;
use App\Models\Tenant\Employee\Announcement\Announcement;

class AnnouncementRequest extends BaseRequest
{
    public function rules()
    {
        return $this->initRules(new Announcement());
    }

    public function messages(): array
    {
        return [
            'start_date.after' => 'The start date must be a date after now'
        ];
    }
}
