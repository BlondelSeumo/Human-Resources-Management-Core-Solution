<?php

namespace Gainhq\Installer\App\Hooks;

use Gainhq\Installer\App\Helpers\Traits\InstanceCreator;

class AfterUserSaved extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        cache()->forget('user-'.$this->model->id);
        cache()->forget('user-roles-permissions-'.$this->model->id);
        cache()->forget('user-roles-'.$this->model->id);
        cache()->forget('auth-user-permissions-'.$this->model->id);
    }
}