<?php


namespace App\Hooks\User;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;

class AfterLogin extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        return $this->model;
    }
}
