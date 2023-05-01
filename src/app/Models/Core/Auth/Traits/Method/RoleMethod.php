<?php

namespace App\Models\Core\Auth\Traits\Method;

/**
 * Trait RoleMethod.
 */
trait RoleMethod
{
    /**
     * @return mixed
     */
    public function isDefault()
    {
        return $this->is_default;
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }

    public static function findByName(string $role_name)
    {
        return self::query()->whereName($role_name)->first();
    }

    public function hasPermission(string $permission_name): bool
    {
        return $this->permissions()
            ->where('name', $permission_name)->exists();
    }
}
