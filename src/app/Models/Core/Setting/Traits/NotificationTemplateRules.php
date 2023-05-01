<?php


namespace App\Models\Core\Setting\Traits;


trait NotificationTemplateRules
{
    public function createdRules()
    {
        return [
            'subject' => 'nullable',
            'custom_content' => 'nullable',
            'type' => 'required|in:sms,mail,database'
        ];
    }
}
