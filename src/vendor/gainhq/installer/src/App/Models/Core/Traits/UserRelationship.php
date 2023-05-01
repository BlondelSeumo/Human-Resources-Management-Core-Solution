<?php

namespace Gainhq\Installer\App\Models\Core\Traits;


use Gainhq\Installer\App\Models\Core\Role;
use Gainhq\Installer\App\Models\Setting;

/**
 * Class UserRelationship.
 */
trait UserRelationship
{
    use CreatedByRelationship;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function settings()
    {
        return $this->morphMany(
            Setting::class,
            'settingable'
        );
    }
}
