<?php


namespace App\Models\Core\Setting\Traits;

use App\Models\Core\Auth\Type;
use App\Models\Core\Notification\NotificationTemplate;
use App\Models\Core\Setting\NotificationSetting;
use App\Models\Core\Traits\TypeRelationship;

trait NotificationEventRelationship
{
    use TypeRelationship;

    public function templates()
    {
        return $this->belongsToMany(
            NotificationTemplate::class,
            'notification_event_template',
            'notification_event_id',
            'notification_template_id'
        );
    }

    public function settings()
    {
        return $this->hasOne(NotificationSetting::class);
    }

}
