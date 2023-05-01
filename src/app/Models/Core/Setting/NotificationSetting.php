<?php

namespace App\Models\Core\Setting;

use App\Models\Core\BaseModel;
use App\Models\Core\Setting\Traits\NotificationAttribute;
use App\Models\Core\Setting\Traits\NotificationSettingMethodTrait as NotificationMethod;
use App\Models\Core\Setting\Traits\NotificationSettingRelationship;
use App\Models\Core\Traits\BootTrait;
use App\Models\Core\Traits\DescriptionGeneratorTrait;


class NotificationSetting extends BaseModel
{
    use NotificationSettingRelationship,
        NotificationAttribute,
        NotificationMethod,
        DescriptionGeneratorTrait;


    protected $table = 'notification_settings';

    protected $fillable = [
        'notification_event_id',
        'updated_by',
        'notify_by',
        'tenant_id'
    ];

    public function createdRules()
    {
        return [
            'notification_event_id' => 'required',
            'notify_by' => 'nullable|array',
            'audiences' => 'nullable|array',
            'audiences.0.audience_type' => 'required_if:audiences.1.audience_type,null|min:2',
            'audiences.1.audience_type' => 'required_if:audiences.0.audience_type,null|min:2',
            'audiences.0.audiences' => 'required_if:audiences.0.audience_type,roles|array',
            'audiences.1.audiences' => 'required_if:audiences.1.audience_type,users|array',
        ];
    }

}
