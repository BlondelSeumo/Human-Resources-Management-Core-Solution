<?php

namespace App\Models\Core\Auth\Traits\Relationship;

use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\RolePermissionPivot;
use App\Models\Core\Auth\User;
use App\Models\Core\Traits\CreatedByRelationship;
use App\Models\Core\Traits\TypeRelationship;

trait RoleRelationship {
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
