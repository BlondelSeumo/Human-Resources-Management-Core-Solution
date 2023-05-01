<?php

namespace App\Repositories\Core\Auth;

use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Repositories\Core\BaseRepository;
use Illuminate\Support\Collection;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * UserRepository constructor.
     *
     * @param  User  $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function findByEmail(string $email)
    {
        return $this->model::query()->where('email', $email)->first();
    }


    public function getCachedAuthUserWithRoleAndPermissions()
    {
        return cache()->remember('user-roles-permissions-'.auth()->id(), 86400, function () {
            return auth()->user()->load('roles:id', 'roles.permissions');
        });
    }

    /**
     * @return Collection
     */
    public function getAuthUserPermissions()
    {
        $user = $this->getCachedAuthUserWithRoleAndPermissions();
        return optional($user->roles)->map(function (Role $role) {
            return collect($role->permissions)->toArray();
        })->flatten(1);
    }

    public function findAuthUserPermission($permission)
    {
        return $this->getAuthUserPermissions()->where('name', $permission)->first();
    }

    public function getCachedRolesUser()
    {
        $user = auth()->user()->load('roles.type');

        return $user->roles;
    }

    public function getPermissionsForFrontEnd()
    {
        $permissions = resolve(UserRepository::class)
            ->getAuthUserPermissions();

        return $permissions->map(function ($permission) {
            return [
                $permission['name'] => true
            ];
        })->reduce(function ($pre, $permission) {
            return array_merge($pre, $permission);
        }, []);
    }

}
