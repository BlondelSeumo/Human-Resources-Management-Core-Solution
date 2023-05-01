<?php

namespace App\Models\Core\Setting;

use App\Models\Core\BaseModel;
use App\Models\Core\Setting\Traits\SettingBoot;
use App\Models\Core\Setting\Traits\SettingRelationship;
use App\Models\Core\Setting\Traits\SettingRules;
use App\Models\Core\Traits\DescriptionGeneratorTrait;

class Setting extends BaseModel
{
    use SettingRelationship, SettingRules, SettingBoot, DescriptionGeneratorTrait;

    protected $fillable = [
        'name', 'value', 'context', 'autoload', 'public', 'settingable_type', 'settingable_id'
    ];

    protected static $logAttributes = [
        'name', 'context'
    ];

    public function matchedService()
    {
        $matched = array_filter(array_keys(config('settings.supported_mail_services')), function ($mail) {
            return preg_match('/'.$mail.'/', $this->attributes['context']);
        });

        return end($matched);
    }
}
