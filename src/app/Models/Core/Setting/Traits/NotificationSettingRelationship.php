<?php


namespace App\Models\Core\Setting\Traits;


use App\Models\Core\Setting\NotificationAudience;
use App\Models\Core\Setting\NotificationEvent;
use App\Models\Core\Traits\UpdatedByRelationship;

trait NotificationSettingRelationship
{
    use UpdatedByRelationship;

    public function notificationEvent()
    {
        return $this->belongsTo(NotificationEvent::class, 'notification_event_id', 'id');
    }

    public function audiences()
    {
        return $this->hasMany(NotificationAudience::class, 'notification_setting_id', 'id');
    }
}
