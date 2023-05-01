<?php


namespace App\Hooks\User;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;
use App\Services\Tenant\Employee\DepartmentService;

class BeforeRoleAttachedToUser extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        $departmentManagerRole = resolve(DepartmentService::class)->departmentManagerRole();
        if ($this->model->hasRole($departmentManagerRole)) {
            request()->merge(['roles' => array_merge(request()->get('roles'), [$departmentManagerRole->id])]);
            return;
        };

        throw_if(in_array($departmentManagerRole->id, request()->get('roles')),
            new GeneralException(__t('action_not_allowed'))
        );
    }
}