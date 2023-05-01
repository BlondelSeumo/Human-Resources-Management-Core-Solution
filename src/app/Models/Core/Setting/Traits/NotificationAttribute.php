<?php


namespace App\Models\Core\Setting\Traits;


trait NotificationAttribute
{
    public function setNotifyByAttribute($notify_by)
    {
        $this->attributes['notify_by'] = json_encode($notify_by);
    }

    public function getNotifyByAttribute()
    {
        return json_decode($this->attributes['notify_by']);
    }

}
