<?php

namespace App\Models\Core\Setting;

use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Models\Core\BaseModel;


class NotificationAudience extends BaseModel
{
    protected $fillable = [
        'notification_setting_id', 'audience_type', 'audiences'
    ];

    /**
     * @return mixed
     */
    public function getAudiencesAttribute()
    {
        return json_decode($this->attributes['audiences']);
    }

    public function setAudiencesAttribute($audiences)
    {
        $this->attributes['audiences'] = json_encode($audiences);
    }

    public function roles()
    {
        if ($this->audience_type == 'roles')
            return Role::findMany($this->audiences);
        return [];
    }

    public function users()
    {
        if ($this->audience_type == 'users')
            return User::findMany($this->audiences);
        return [];
    }
}
