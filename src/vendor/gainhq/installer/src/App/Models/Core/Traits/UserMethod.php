<?php

namespace Gainhq\Installer\App\Models\Core\Traits;

use Gainhq\Installer\App\Models\Core\Role;
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

    public function assignRole($role)
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
                ->whereHas('type', function (Builder $query) use ($type) {
                    $query->where('alias', $type);
                })
                ->exists();
        });
    }

    public static function findByEmail(string $email)
    {
        return self::where('email', $email)->whereHas('status', function (Builder $builder) {
            $builder->whereNotIn('name', ['status_inactive', 'status_invited']);
        })->firstOrFail();
    }

    public function assignSocialLinks($socialLinks){
        return $this->socialLinks()->attach($socialLinks);
    }

}
