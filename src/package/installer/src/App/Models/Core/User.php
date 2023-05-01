<?php

namespace Gainhq\Installer\App\Models\Core;

use Altek\Eventually\Eventually;
use Gainhq\Installer\App\Models\Core\Traits\HasRoles;
use Gainhq\Installer\App\Models\Core\Traits\UserAttribute;
use Gainhq\Installer\App\Models\Core\Traits\UserBootTrait;
use Gainhq\Installer\App\Models\Core\Traits\UserMethod;
use Gainhq\Installer\App\Models\Core\Traits\UserRelationship;
use Gainhq\Installer\App\Models\Core\Traits\UserRules;
use Gainhq\Installer\App\Models\Core\Traits\UserScope;
use Gainhq\Installer\App\Models\Core\Traits\UserStatus;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\CausesActivity;


class User extends BaseUser implements HasLocalePreference
{
    protected static $logAttributes = [
        'first_name', 'last_name', 'email'
    ];

    use UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope,
        HasRoles,
        UserRules,
        UserBootTrait,
        Eventually,
        Notifiable,
        CausesActivity,
        UserStatus;

    public function preferredLocale()
    {
        return app()->getLocale() ?? 'en';
    }

    public function getDescriptionForEvent(string $eventName) : string
    {
        $class_alias_array = explode('\\', get_called_class());
        $class_name = strtolower(end($class_alias_array));

        return trans('default.log_description_message', [
            'model' => trans('default.'.$class_name),
            'event' => trans('default.'.$eventName)
        ]);
    }

}
