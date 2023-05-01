<?php

namespace Gainhq\Installer\App\Models\Core;

use Altek\Eventually\Eventually;
use Gainhq\Installer\App\Models\Core\Traits\SendUserPasswordReset;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User.
 */
abstract class BaseUser extends Authenticatable
{
    use Eventually,
        Notifiable,
        SendUserPasswordReset,
        SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'active',
        'last_login_at',
        'created_by',
        'status_id',
        'invitation_token'
    ];

    /**
     * The dynamic attributes from mutators that should be returned with the user object.
     * @var array
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Return true or false if the user can impersonate an other user.
     *
     * @param void
     * @return  bool
     */
    public function canImpersonate()
    {
        return $this->isAdmin();
    }

    /**
     * Return true or false if the user can be impersonate.
     *
     * @param void
     * @return  bool
     */
    public function canBeImpersonated()
    {
        return $this->id !== 1;
    }
}
