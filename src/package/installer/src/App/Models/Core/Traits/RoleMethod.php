<?php

namespace Gainhq\Installer\App\Models\Core\Traits;

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
}
