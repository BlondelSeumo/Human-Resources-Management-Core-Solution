<?php


namespace App\Services\Core\Auth;


use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Services\Core\BaseService;

class UserInvitationService extends BaseService
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function invite($email, $roles = [])
    {
        $roles = count($roles) ? $roles : request()->get('roles');

        $this->create($email)->assignRoles($roles);

        $this->model->invite();

        return $this->model;
    }

    public function create($email, array $attributes = [])
    {
        $status = Status::findByNameAndType('status_invited')->id;

        $invitation_token = base64_encode($email.'-invitation-from-us');

        $this->model->fill(array_merge([
                'email' => $email,
                'status_id' => $status,
                'invitation_token' => $invitation_token
            ], $attributes))->save();

        return $this;
    }

    public function assignRoles($roles)
    {
        foreach ($roles as $key => $role) {
            $this->model->assignRole($role);
        }

        return $this;
    }

    public function detachRoles()
    {
        $this->model->roles()->sync([]);

        return $this;
    }

    public function delete()
    {
        $this->model->forceDelete();

        return true;
    }
}
