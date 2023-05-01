<?php

namespace App\Http\Controllers\Core\Auth\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Auth\Role\RolePermissionRequest as Request;
use App\Models\Core\Auth\Role;
use App\Services\Core\Auth\RoleService;

class RolePermissionController extends Controller
{
    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function store(Role $role, Request $request)
    {
        $old_permissions = $role->permissions;

        $role = $this->service
            ->attachPermissions($role);

        attach_log_to_database('permissions', 'role', $old_permissions, $role->permissions);

        return attached_response('permissions', ['role' => $role]);
    }

    public function update(Role $role, Request $request)
    {
        $old_permissions = $role->permissions;

        $role = $this->service
            ->detachPermissions($role);

        detach_log_to_database('permissions', 'role', $old_permissions, $role->permissions);

        return detached_response('permissions', ['role' => $role]);
    }
}
