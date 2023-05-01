<?php


namespace App\Hooks\Department;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;
use App\Models\Tenant\Employee\Department;

class DepartmentCreated extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        /**@var $department Department*/
        $department = $this->model;

        if ($department->manager) {
            $hasManager = $department->manager->roles()->where('is_default', 1)->first();
        }

    }
}
