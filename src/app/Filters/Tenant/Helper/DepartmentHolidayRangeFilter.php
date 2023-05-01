<?php


namespace App\Filters\Tenant\Helper;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

trait DepartmentHolidayRangeFilter
{
    public function departmentHolidayFilter($ranges)
    {
        return function (BelongsToMany $builder) use ($ranges) {
            $builder->select('id', 'name', 'start_date', 'end_date');

            if (count($ranges) == 1) {
                return $builder->whereDate('start_date', $ranges[0]);
            }

            return $builder->whereBetween(DB::raw('DATE(start_date)'),  $ranges)
                ->whereBetween(DB::raw('DATE(end_date)'), $ranges);

        };
    }
}