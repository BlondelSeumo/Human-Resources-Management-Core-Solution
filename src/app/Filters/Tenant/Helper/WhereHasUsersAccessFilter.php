<?php


namespace App\Filters\Tenant\Helper;


use App\Filters\FilterBuilder;
use App\Helpers\Traits\UserAccessQueryHelper;
use Illuminate\Database\Eloquent\Builder;

class WhereHasUsersAccessFilter extends FilterBuilder
{
    use UserAccessQueryHelper;

    public function showAll($showAll = 'yes')
    {
        $this->builder->when($showAll == 'yes' && request()->get('access_behavior') == 'own_departments',
            fn(Builder $b) => $b
                ->whereHas('users', fn(Builder $builder) => $this->userAccessQuery($builder, 'id'))
        );
    }
}