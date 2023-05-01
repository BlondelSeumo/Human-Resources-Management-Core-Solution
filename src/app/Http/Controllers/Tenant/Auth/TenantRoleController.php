<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Filters\Core\RoleFilter;
use App\Http\Controllers\Core\Auth\Role\RoleController;
use App\Http\Requests\Core\Auth\Role\RoleRequest;
use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Role;
use App\Services\Core\Auth\RoleService;

class TenantRoleController extends RoleController
{
    public function __construct(RoleService $roleService, RoleFilter $filter)
    {
        $this->service = $roleService;
        $this->filter = $filter;
        parent::__construct($roleService, $filter);
    }

    public function store(RoleRequest $request)
    {
        $this->service->save(
            $request->except('is_admin')
        );

        if ($request->get('is_manager')) {
            $access_all = Permission::query()->where('name', 'access_all_departments')->first();
            $permissions = array_merge($request->get('permissions', []), [['permission_id' => $access_all->id]]);
        } else {
            $permissions = $request->get('permissions', []);
        }

        $this->service->notify('roles_created')
            ->assignPermissions($permissions);


        return created_responses('role');
    }

    public function update(Role $role, RoleRequest $request)
    {
        $has_access_all_departments = $role->hasPermission('access_all_departments');
        if (!$has_access_all_departments && $request->get('is_manager')) {
            $access_all = Permission::query()->where('name', 'access_all_departments')->first();
            $request->merge(['permissions' => array_merge($request->get('permissions', []), [['permission_id' => $access_all->id]])]);
        } else if ($has_access_all_departments && !$request->get('is_manager')) {
            $access_all = Permission::query()->where('name', 'access_all_departments')->first();
            $permissions = collect($request->get('permissions', []))
                ->filter(fn($permission) => $permission['permission_id'] != $access_all->id)
                ->toArray();
            $request->merge(['permissions' => $permissions]);
        }
        $this->service->setModel($role)
            ->setAttributes(array_merge($request->except('is_admin'), ['is_default' => $request->get('is_manager')]))
            ->validateDepartmentAccess()
            ->update();

        return updated_responses('role');
    }
}
