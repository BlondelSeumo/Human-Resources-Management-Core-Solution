<?php


namespace App\Hooks\User;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;

class BeforeUserAttachedToRole extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        collect(request()->get('users',[]))->map(function ($user_id) {
            cache()->forget('app-admin-'.$user_id);
        });
    }
}