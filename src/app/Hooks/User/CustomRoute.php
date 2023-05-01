<?php

namespace App\Hooks\User;

use App\Hooks\HookContract;
use App\Helpers\Core\Traits\InstanceCreator;

class CustomRoute extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        return [];
    }
}
