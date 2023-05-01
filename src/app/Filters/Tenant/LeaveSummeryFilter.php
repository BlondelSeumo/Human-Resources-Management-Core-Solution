<?php


namespace App\Filters\Tenant;


use App\Filters\FilterBuilder;
use App\Filters\Traits\DateRangeFilter;
use App\Filters\Traits\FilterThroughDepartment;
use App\Filters\Traits\SearchThroughUserFilter;
use App\Filters\Traits\WorkingShiftFilter;
use App\Helpers\Traits\DateTimeHelper;
use App\Helpers\Traits\MakeArrayFromString;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class LeaveSummeryFilter extends FilterBuilder
{
    use DateRangeFilter,
        MakeArrayFromString,
        SearchThroughUserFilter,
        WorkingShiftFilter,
        FilterThroughDepartment,
        DateTimeHelper;

    public function applyDate($date = null)
    {
        $this->builder->when(
            $date,
            fn(Builder $builder) => $builder->whereDate('created_at', $this->carbon($date)->parse()),
            fn(Builder $builder) => $builder->whereDate('created_at', todayFromApp())
        );
    }

    public function leaveDuration($value = null)
    {
        $this->whereClause('duration_type', $value);
    }

}
