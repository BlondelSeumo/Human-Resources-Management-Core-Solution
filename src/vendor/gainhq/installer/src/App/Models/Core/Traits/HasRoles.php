<?php


namespace Gainhq\Installer\App\Models\Core\Traits;


use App\Models\Core\Auth\Role;

trait HasRoles {

    public function hasRole($roles)
    {
        if (is_string($roles))
            return $this->roles->contains('name', $roles);

        if (is_int($roles))
            return $this->roles->contains('id', $roles);

        if ($roles instanceof Role)
            return $this->roles->contains('id', $roles->id);

        if (is_array($roles)){
            foreach ($roles as $key => $role) {
                if ($this->hasRole($role))
                    return true;
                break;
            }
            return false;
        }

        return collect($roles)->intersect($this->roles)->isNotEmpty();

    }
}
