<?php


namespace App\Filters\Tenant;


use App\Filters\FilterBuilder;
use App\Filters\Tenant\Helper\DepartmentHolidayRangeFilter;
use App\Filters\Traits\FilterThroughDepartment;
use App\Filters\Traits\SearchThroughUserFilter;
use App\Helpers\Traits\MakeArrayFromString;
use App\Helpers\Traits\UserAccessQueryHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LeaveStatusFilter extends FilterBuilder
{
    use MakeArrayFromString,
        FilterThroughDepartment,
        SearchThroughUserFilter,
        DepartmentHolidayRangeFilter,
        UserAccessQueryHelper;

    public function workingShifts($working_shifts = null)
    {
        $working_shifts = $this->makeArray($working_shifts);

        $this->builder->when(count($working_shifts),
            fn(Builder $builder) => $builder->whereHas('workingShiftDetails',
                fn(Builder $builder) => $builder->whereIn('working_shift_id', $working_shifts)
            ));
    }

    public function dateRange($date_range = null)
    {
        $period = json_decode(htmlspecialchars_decode($date_range), true);

        $this->builder->when($period, function (Builder $builder) use ($period) {
            $builder->where(function (Builder $builder) use ($period) {
                $builder->whereBetween('start_at', array_values($period))
                    ->orWhereBetween('end_at', array_values($period))
                    ->orWhere(function ($query) use ($period) {
                        $query->whereDate('start_at', '<=', $period['start'])
                            ->whereDate('end_at', '>=', $period['end']);
                    });
            });
        });
    }

    public function leaveDuration($value = null)
    {
        $this->whereClause('duration_type', $value);
    }

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

    public function users($users = null): void
    {
        $users = $this->makeArray($users);

        $this->builder->when(count($users), function (Builder $builder) use ($users) {
            $builder->whereHas(
                'user', fn(Builder $builder) => $builder->whereIn('id', $users)
            );
        });
    }

}