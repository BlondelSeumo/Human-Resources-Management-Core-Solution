<?php

namespace App\Models\Core\Setting;

use App\Models\Core\BaseModel;
use App\Models\Core\Setting\Traits\NotificationEventRelationship;
use App\Models\Core\Setting\Traits\NotificationEventRules;
use App\Models\Core\Traits\Translate\TranslatedNameTrait;
use Illuminate\Database\Eloquent\Builder;

class NotificationEvent extends BaseModel
{
    protected $appends = ['translated_name'];

    use NotificationEventRelationship, NotificationEventRules;

    public function getTranslatedNameAttribute()
    {
        $explode = explode('_', $this->name);

        if (count($explode) < 2) {
            return  __t($explode[0]);
        }

        if (count($explode) == 2) {
            return trans('default.notification_event_name', [
                'name' => __t($explode[0]),
                'action' => __t('notification_'.(isset($explode[1]) ? $explode[1] : '')),
            ]);
        }

        if (count($explode) == 3) {
            return trans('default.notification_event_name', [
                'name' => __t("{$explode[0]}_{$explode[1]}"),
                'action' => __t('notification_'.$explode[2])
            ]);
        }

        return __t($this->name);

    }

}
