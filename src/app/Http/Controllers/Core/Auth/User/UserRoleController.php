<?php

namespace App\Http\Controllers\Core\Auth\User;

use App\Hooks\User\BeforeUserAttachedToRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Auth\User\UserRoleRequest;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Services\Core\Auth\UserService;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }


    public function store(User $user, UserRoleRequest $request)
    {
        $user = $this->service
            ->setModel($user)
            ->beforeAttachingRole()
            ->attachRole();

        return attached_response('roles', ['user' => $user]);
    }



    public function update(User $user, UserRoleRequest $request)
    {
        $user = $this->service
            ->setModel($user)
            ->beforeDetachingRole()
            ->detachRole();

        return detached_response('roles', ['user' => $user]);
    }

    public function attachUsers(Role $role, Request $request)
    {
        $request->validate([
            'users' => 'required|array'
        ]);

        BeforeUserAttachedToRole::new()
            ->setModel($role)
            ->handle();

        $role->users()->detach($request->get('users'));

        $role->users()->attach($request->get('users'));

        return attached_response('users');

    }

}
