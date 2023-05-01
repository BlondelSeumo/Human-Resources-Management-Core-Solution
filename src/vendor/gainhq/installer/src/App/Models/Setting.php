<?php

namespace Gainhq\Installer\App\Models;

use Gainhq\Installer\App\Models\Core\BaseModel;
use Gainhq\Installer\App\Models\Core\Traits\DescriptionGeneratorTrait;
use Gainhq\Installer\App\Models\Core\Traits\SettingBoot;
use Gainhq\Installer\App\Models\Core\Traits\SettingRelationship;
use Gainhq\Installer\App\Models\Core\Traits\SettingRules;

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
            return preg_match('/' . $mail . '/', $this->attributes['context']);
        });

        return end($matched);
    }
}
