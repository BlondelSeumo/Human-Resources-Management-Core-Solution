<?php


namespace App\Hooks\User;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;
use App\Services\Tenant\Employee\EmployeeInviteService;

class BeforeInvitationCanceled extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        resolve(EmployeeInviteService::class)
            ->setModel($this->model)
            ->cancel();

        return $this->model;
    }
}