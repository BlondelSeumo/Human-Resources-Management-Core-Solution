<?php


namespace App\Filters\Common\Notification;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Models\Core\Setting\NotificationSetting;

class NotificationSettingsVerifier
{
    use InstanceCreator;

    public function verify(NotificationSetting $setting)
    {
        return $setting;
    }
}
