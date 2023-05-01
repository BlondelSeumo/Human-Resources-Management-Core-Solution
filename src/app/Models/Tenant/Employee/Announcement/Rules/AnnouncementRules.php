<?php

namespace App\Models\Tenant\Employee\Announcement\Rules;


trait AnnouncementRules
{
    public function createdRules()
    {
        return [
            'name' => 'required|min:2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'required|string',
            'departments' => 'nullable|array',
            'departments.*' => 'required|exists:departments,id'
        ];
    }

    public function updatedRules()
    {
        return $this->createdRules();
    }
}