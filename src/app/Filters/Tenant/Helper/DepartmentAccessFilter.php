<?php


namespace App\Filters\Tenant\Helper;


use App\Filters\FilterBuilder;
use App\Helpers\Traits\UserAccessQueryHelper;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use Illuminate\Database\Eloquent\Builder;

class DepartmentAccessFilter extends FilterBuilder
{
    use UserAccessQueryHelper;

    public function showAll($showAll = 'yes')
    {
        $this->builder->when($showAll == 'yes' && request()->get('access_behavior') == 'own_departments',
            function (Builder $builder) {
                $managerDpt = resolve(DepartmentRepository::class)->getDepartments(auth()->id());
                $builder->whereIn('id', $managerDpt);
            }
        );
    }
}