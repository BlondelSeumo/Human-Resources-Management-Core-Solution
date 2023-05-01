<?php


namespace App\Filters\Tenant;


use App\Helpers\Traits\UserAccessQueryHelper;
use Illuminate\Database\Eloquent\Builder;

class AttendanceDailLogFilter extends AttendanceRequestsFilter
{
    use UserAccessQueryHelper;

    public function showAll($showAll = 'yes')
    {
        $this->builder->when($showAll == 'no', function (Builder $builder) {
                $builder->where('user_id', auth()->id());
            },function (Builder $builder) {
            $builder->when(request()->get('access_behavior') == 'own_departments',
                fn(Builder $b) => $this->userAccessQuery($b)
            );
        });
    }
}