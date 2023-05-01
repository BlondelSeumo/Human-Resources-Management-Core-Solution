<?php


namespace App\Filters\Tenant;


use Illuminate\Database\Eloquent\Builder;

class AttendanceSummaryFilter extends AttendanceDailLogFilter
{
    public function rangeFilter($attendanceActive, $ranges)
    {
        return function (Builder $builder) use ($attendanceActive, $ranges) {
            if (count($ranges) == 1) {
                return $builder->whereDate('in_date', $ranges[0])
                    ->where('status_id', $attendanceActive);
            }

            return $builder->whereDate('in_date', '>=', $ranges[0])
                ->where('status_id', $attendanceActive)
                ->whereHas(
                    'details',
                    fn(Builder $bl) => $bl->whereDate('out_time', '<=', $ranges[1])
                        ->where('status_id', $attendanceActive)
                );
        };
    }
}