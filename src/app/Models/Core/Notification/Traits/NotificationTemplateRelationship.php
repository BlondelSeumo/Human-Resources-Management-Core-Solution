<?php


namespace App\Models\Core\Notification\Traits;


use App\Models\Core\Setting\NotificationEvent;

trait NotificationTemplateRelationship
{
    public function events()
    {
        return $this->belongsToMany(
            NotificationEvent::class,
            'notification_event_template',
            'notification_template_id',
            'notification_event_id'
        );
    }
}
