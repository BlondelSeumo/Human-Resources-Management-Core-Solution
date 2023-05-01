<?php


namespace App\Hooks\User;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;
use App\Jobs\Tenant\AssignLeaveByStatusJob;
use App\Jobs\Tenant\AssignLeaveJob;

class AfterUserConfirmed extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        AssignLeaveByStatusJob::dispatch();
        AssignLeaveJob::dispatch();
        return $this->model;
    }
}
