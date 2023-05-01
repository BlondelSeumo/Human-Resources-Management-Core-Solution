<?php


namespace App\Hooks\User;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;

class BeforeUserDeleted extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        throw new GeneralException(trans('default.action_not_allowed'));
    }
}