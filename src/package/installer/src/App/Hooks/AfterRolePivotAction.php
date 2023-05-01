<?php


namespace Gainhq\Installer\App\Hooks;

use Gainhq\Installer\App\Helpers\Traits\InstanceCreator;
use Gainhq\Installer\App\Models\Core\User;

class AfterRolePivotAction extends HookContract
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