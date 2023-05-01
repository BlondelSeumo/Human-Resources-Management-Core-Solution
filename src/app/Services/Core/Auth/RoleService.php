<?php


namespace App\Services\Core\Auth;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\HasWhen;
use App\Hooks\User\BeforeRoleCreated;
use App\Hooks\User\BeforeRoleUpdated;
use App\Hooks\User\WhileRoleDeleting;
use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Role;
use App\Notifications\Core\Role\RoleNotification;
use App\Services\Core\BaseService;

class RoleService extends BaseService
{
    use HasWhen;

    /**
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function validateDepartmentAccess(): RoleService
    {
        if ($this->model->alias == 'department_manager') {
            $all_department_permission = Permission::query()->where('group_name', 'access_department')
                ->where('name', 'access_all_departments')->first();
            collect(request()->get('permissions'))->map(function ($item) use ($all_department_permission) {
                if ($item['permission_id'] == $all_department_permission->id) {
                    throw new GeneralException(__t('action_not_allowed'));
                }
            });
        } else {
            $only_department_permission = Permission::query()->where('group_name', 'access_department')
                ->where('name', 'access_own_departments')->first();
            collect(request()->get('permissions'))->map(function ($item) use ($only_department_permission) {
                if ($item['permission_id'] == $only_department_permission->id) {
                    throw new GeneralException(__t('action_not_allowed'));
                }
            });
        }

        return $this;
    }

    public function update()
    {
        throw_if(
            $this->model->isAdmin() || $this->model->alias == 'employee'|| $this->model->alias == 'department_manager',
            new GeneralException(__t('action_not_allowed'))
        );

        $this->model->fill($this->getFillAble(request()->except('is_default', 'is_admin')));

        $condition = $this->model->isDirty() || ($this->model->permissions()->count() !== count(request()->get('permissions', [])));

        $this->when($condition, function (RoleService $service) {
            $service->notify('roles_updated');
        })->when(count(request()->get('permissions', [])), function (RoleService $service) {
            $service->syncPermissions(request()->get('permissions'));
        });

        $this->model->save();

        return $this->model;
    }

    public function delete()
    {
        if ($this->model->isDefault())
            throw new GeneralException(trans('default.default_delete', ['name' => 'role']));

        $this->model->delete();

        return $this;
    }


    public function assignPermissions(array $permissions, $attach = false)
    {
        if (!$attach)
            return $this->model->permissions()->sync($permissions);

        foreach ($permissions as $permission) {
            $this->model->permissions()->attach($permission['permission_id'], [
                'meta' => json_encode(isset($permission['meta']) ? $permission['meta'] : [])
            ]);
        }

        return $this;
    }


    public function syncPermissions(array $permissions)
    {
        $this->model->permissions()->sync([]);
        return $this->assignPermissions($permissions);
    }


    public function attachPermissions(Role $role)
    {
        if ($role->isDefault())
            throw new GeneralException(trans('default.default_update', ['name' => 'role']));

        $permissions = is_array(request('permissions')) ? request('permissions') : [request('permissions')];
        foreach ($permissions as $key => $permission) {
            if (!$role->permissions->contains($permission)) {
                $role
                    ->permissions()
                    ->attach($permission);
            }
        }

        return $role->load('permissions');
    }

    public function detachPermissions(Role $role)
    {
        $permissions = is_array(request('permissions')) ? request('permissions') : [request('permissions')];

        $role
            ->permissions()
            ->detach($permissions);

        return $role->load("permissions");
    }

    public function beforeCreated()
    {
        BeforeRoleCreated::new()
            ->handle();

        return $this;
    }

    public function beforeUpdated()
    {
        BeforeRoleUpdated::new()
            ->setModel($this->model)
            ->handle();

        return $this;
    }

    public function whileDeleting()
    {
        WhileRoleDeleting::new()
            ->setModel($this->model)
            ->handle();

        return $this;
    }

    public function notify(string $event, $role = null)
    {
        notify()
            ->on($event)
            ->with($role ?: $this->model)
            ->send(RoleNotification::class);

        return $this;
    }



}
