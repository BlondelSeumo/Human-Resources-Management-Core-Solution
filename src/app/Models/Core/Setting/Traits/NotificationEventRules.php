<?php


namespace App\Models\Core\Setting\Traits;

use App\Models\Core\Notification\NotificationTemplate;

trait NotificationEventRules
{
    public function attachedRules()
    {
        return [
            'templates' => 'required'
        ];
    }
}
