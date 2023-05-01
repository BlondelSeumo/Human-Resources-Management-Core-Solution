<?php


namespace App\Hooks\User;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;

class WhileRoleDeleting extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        throw_if(
            in_array(optional($this->model)->alias, ['manager', 'employee', 'department_manager'])
            || optional($this->model)->is_default == 1,
            new GeneralException(__t('action_not_allowed'))
        );
    }
}
