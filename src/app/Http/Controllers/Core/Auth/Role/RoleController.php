<?php

namespace App\Http\Controllers\Core\Auth\Role;

use App\Filters\Common\Auth\RoleFilter as AppRoleFilter;
use App\Filters\Core\RoleFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Auth\Role\RoleRequest;
use App\Models\Core\Auth\Role;
use App\Notifications\Core\Role\RoleNotification;
use App\Services\Core\Auth\RoleService;

class RoleController extends Controller
{

    public function __construct(RoleService $roleService, RoleFilter $filter)
    {
        $this->service = $roleService;
        $this->filter = $filter;
    }


    public function index()
    {
        return (new AppRoleFilter(
            $this->service
                ->with('users:id,first_name,last_name,email', 'users.profilePicture')
                ->orderBy('id')
                ->filters($this->filter)
        ))->filter()
            ->paginate(request()->get('per_page', 10));
    }


    public function store(RoleRequest $request)
    {
        $this->service
            ->beforeCreated()
            ->save(request()->except('is_default', 'is_admin'));

        $this->service
            ->notify('roles_created')
            ->when(count($request->get('permissions', [])), function (RoleService $service) use ($request) {
                $service->assignPermissions($request->get('permissions'));
            });

        return created_responses('role');
    }

    public function show(Role $role)
    {
        $role = $role->load('permissions')->toArray();
        if (request()->group_permission) {
            $role['permissions'] = collect($role['permissions'])->groupBy(function ($permission) {
                return $permission['group_name'];
            });
        }
        return $role;
    }


    public function edit(RoleRequest $request, Role $role)
    {
        if ($role->isAdmin()) {
            return redirect()->back()->withFlashDanger(__t('action_not_allowed'));
        }
        return view('core.auth.role.edit')
            ->withRole($role)
            ->withRolePermissions($role->permissions->pluck('name')->all());
    }

    public function update(Role $role, RoleRequest $request)
    {
        $this->service
            ->setModel($role)
            ->beforeUpdated()
            ->update();

        return updated_responses('role');
    }

    public function destroy(Role $role, RoleRequest $request)
    {
        $this->service
            ->setModel($role)
            ->whileDeleting()
            ->delete();

        notify()
            ->on('roles_deleted')
            ->with((object)$role->toArray())
            ->send(RoleNotification::class);

        return deleted_responses('role');
    }
}
