<?php


namespace App\Helpers\Traits;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\HasWhen;
use App\Repositories\Tenant\Employee\DepartmentRepository;

trait DepartmentAuthentications
{
    use HasWhen;

    public function departmentAuthentications($id, $withUser = false, $action = 'user')
    {
        $this->when(request()->has('access_behavior') &&
            request()->get('access_behavior') == 'own_departments' && $id,
            function () use ($id, $withUser, $action){
                $data = $action == 'user' ? resolve(DepartmentRepository::class)->getDepartmentUsers(auth()->id()) :
                    resolve(DepartmentRepository::class)->getDepartments(auth()->id());
                $condition = $withUser ? $id != auth()->id() : true;
                throw_if(
                    !in_array($id, $data) && $condition,
                    new GeneralException(__t('action_not_allowed'))
                );
            }
        );
    }
}