<?php


namespace App\Hooks\User;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;
use App\Models\Core\Auth\User;

class AfterRoleSaved extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        optional(optional($this->model)->users)->map(function (User $user) {
            cache()->forget('user-'.$user->id);
            cache()->forget('user-roles-permissions-'.$user->id);
            cache()->forget('user-roles-'.$user->id);
            cache()->forget('auth-user-permissions-'.$user->id);
        });
    }
}