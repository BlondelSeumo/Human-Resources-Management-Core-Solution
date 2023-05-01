<?php


namespace App\Filters\Tenant;


use App\Filters\Tenant\Helper\DepartmentHolidayRangeFilter;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\UserAccessQueryHelper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceDetailsFilter extends EmployeeFilter
{
    use DateRangeHelper, DepartmentHolidayRangeFilter;

    public function workingShifts($workingShifts = null): void
    {
        $workingShifts = $this->makeArray($workingShifts);

        $this->builder->when(count($workingShifts), function (Builder $builder) use ($workingShifts) {
            $builder->whereHas(
                'attendances',
                fn(Builder $b) => $b->whereIn('working_shift_id', $workingShifts)
            );
        });
    }

    public function detailsFilter($attendanceActive, $ranges)
    {
        return function (HasMany $builder) use ($attendanceActive, $ranges) {
            $builder->select(['id', 'in_date', 'user_id']);

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

    public function users($users = null): void
    {
        $users = $this->makeArray($users);

        $this->builder->when(count($users), function (Builder $builder) use ($users) {
            $builder->whereIn('id', $users);
        });
    }


}