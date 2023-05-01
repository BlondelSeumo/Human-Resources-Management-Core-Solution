<?php


namespace Gainhq\Installer\App\Hooks;

use Gainhq\Installer\App\Helpers\Traits\InstanceCreator;

class AfterLogin extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        return $this->model;
    }
}
