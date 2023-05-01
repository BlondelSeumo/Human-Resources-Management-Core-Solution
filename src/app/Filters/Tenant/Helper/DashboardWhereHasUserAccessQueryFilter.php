<?php


namespace App\Filters\Tenant\Helper;


use App\Filters\FilterBuilder;
use App\Helpers\Traits\UserAccessQueryHelper;
use Illuminate\Database\Eloquent\Builder;

class DashboardWhereHasUserAccessQueryFilter extends FilterBuilder
{
    use UserAccessQueryHelper;

    public function showAll($showAll = 'yes')
    {
        $this->builder->when($showAll == 'yes' && request()->get('access_behavior') == 'own_departments',
            fn(Builder $b) => $this->userAccessQuery($b, 'user_id', false)
        );
    }
}