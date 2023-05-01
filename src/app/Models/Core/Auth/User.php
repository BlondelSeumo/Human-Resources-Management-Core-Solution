<?php

namespace App\Models\Core\Auth;

use Altek\Eventually\Eventually;
use App\Models\Core\Auth\Traits\Attribute\UserAttribute;
use App\Models\Core\Auth\Traits\Boot\UserBootTrait;
use App\Models\Core\Auth\Traits\Method\HasRoles;
use App\Models\Core\Auth\Traits\Method\UserMethod;
use App\Models\Core\Auth\Traits\Method\UserStatus;
use App\Models\Core\Auth\Traits\Relationship\UserRelationship;
use App\Models\Core\Auth\Traits\Rules\UserRules;
use App\Models\Core\Auth\Traits\Scope\UserScope;
use App\Models\Tenant\Traits\EmployeeMethods;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Activitylog\Traits\LogsActivity;


class User extends BaseUser implements HasLocalePreference
{

    use UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope,
        HasRoles,
        UserRules,
        UserBootTrait,
        LogsActivity,
        Eventually,
        Notifiable,
        CausesActivity,
        UserStatus,
        EmployeeMethods;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email']);

        // Chain fluent methods for configuration options
    }
}
