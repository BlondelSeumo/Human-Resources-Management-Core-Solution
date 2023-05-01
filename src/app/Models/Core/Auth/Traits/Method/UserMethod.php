<?php

namespace App\Models\Core\Auth\Traits\Method;

use App\Exceptions\GeneralException;
use App\Mail\Core\User\UserInvitationMail;
use App\Models\Core\Auth\Role;
use Illuminate\Database\Eloquent\Builder;
use Mail;

/**
 * Trait UserMethod.
 */
trait UserMethod
{
    /**
     * @return mixed
     */
    public function canChangeEmail()
    {
        return config('access.users.change_email');
    }

    /**
     * @return bool
     */
    public function canChangePassword()
    {
        return ! app('session')->has(config('access.socialite_session_name'));
    }

    /**
     * @param $provider
     *
     * @return bool
     */
    public function hasProvider($provider)
    {
        foreach ($this->providers as $p) {
            if ($p->provider == $provider) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function isAdmin()
    {
        return $this->hasRole(config('access.users.app_admin_role'));
    }

    public function assignRole($role): bool
    {
        if ($this->hasRole($role)) {
            return true;
        }

        if (is_string($role)) {
            return $this->roles()->attach(
                Role::findByName($role)->id
            );
        }

        return $this->roles()->attach($role instanceof Role ? $role->id : $role);
    }

    public function isBrandAdmin($brand_id = null)
    {
        return $this->admin('brand', $brand_id);
    }

    public function isAppAdmin()
    {
        return $this->admin();
    }

    /**
     * @param string $type
     * @param null $brand_id
     * @return mixed
     * @throws \Exception
     *
     * Basically the result is cached. it will be deleted if any updated is happens in user model
     */
    public function admin($type = 'app', $brand_id = null)
    {
        return cache()->remember($type.'-admin-'.$this->id, 84000, function () use ($type, $brand_id) {
            return $this->roles()
                ->where('is_admin', 1)
                ->where('is_default', 1)
                ->when($brand_id, function (Builder $query) use ($brand_id) {
                    $query->where('brand_id', $brand_id);
                })
                ->whereHas('type', function (Builder $query) use ($type) {
                    $query->where('alias', $type);
                })
                ->exists();
        });
    }

    public static function findByEmail(string $email)
    {
        try {
            return self::where('email', $email)->whereHas('status', function (Builder $builder) {
                $builder->whereNotIn('name', ['status_inactive', 'status_invited']);
            })->firstOrFail();
        }catch (\Exception $exception) {
            throw new GeneralException(__t('the_user_you_are_looking_for_is_not_found'));
        }
    }

    public function invite()
    {
        return Mail::to($this->email)
            ->locale(app()->getLocale())
            ->send((new UserInvitationMail($this))->onQueue('high')->delay(5));
    }

}
