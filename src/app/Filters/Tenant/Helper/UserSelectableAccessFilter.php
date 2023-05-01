<?php


namespace App\Filters\Tenant\Helper;


use App\Filters\FilterBuilder;
use App\Helpers\Traits\UserAccessQueryHelper;
use Illuminate\Database\Eloquent\Builder;

class UserSelectableAccessFilter extends FilterBuilder
{
    use UserAccessQueryHelper;

    public function showAll($showAll = 'yes')
    {
        $this->builder->when($showAll == 'no', function (Builder $builder) {
            $builder->where('id', auth()->id());
        },function (Builder $builder) {
            $builder->when(request()->get('access_behavior') == 'own_departments',
                fn(Builder $b) => $this->userAccessQuery($b, 'id', request()->has('with_auth'))
            );
        });
    }
}