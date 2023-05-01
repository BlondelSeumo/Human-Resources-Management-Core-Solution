<?php

namespace Gainhq\Installer\App\Models\Core\Traits;

use Gainhq\Installer\App\Models\Core\Permission;
use Gainhq\Installer\App\Models\Core\RolePermissionPivot;

trait RoleRelationship
{
    use CreatedByRelationship, TypeRelationship;

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permission',
            'role_id',
            'permission_id'
        )->withPivot('meta')
            ->using(RolePermissionPivot::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }


}
