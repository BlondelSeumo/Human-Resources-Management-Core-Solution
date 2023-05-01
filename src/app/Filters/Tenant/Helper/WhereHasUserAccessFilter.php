<?php


namespace App\Filters\Tenant\Helper;


use App\Filters\FilterBuilder;
use App\Helpers\Traits\UserAccessQueryHelper;
use Illuminate\Database\Eloquent\Builder;

class WhereHasUserAccessFilter extends FilterBuilder
{
    use UserAccessQueryHelper;

    public function showAll($showAll = 'yes')
    {
        $this->builder->when($showAll == 'yes' && request()->get('access_behavior') == 'own_departments',
            fn(Builder $b) => $this->userAccessQuery($b)
        );
    }
}