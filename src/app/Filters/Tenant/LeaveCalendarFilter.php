<?php


namespace App\Filters\Tenant;


use App\Filters\Tenant\Helper\DepartmentHolidayRangeFilter;
use App\Filters\Traits\FilterThroughDepartment;
use Illuminate\Database\Eloquent\Builder;

class LeaveCalendarFilter extends LeaveRequestFilter
{
    use DepartmentHolidayRangeFilter;

    public function workingShifts($working_shifts = null)
    {
        $working_shifts = $this->makeArray($working_shifts);

        $this->builder->when(
            count($working_shifts),
            function (Builder $builder) use($working_shifts) {
                $builder->whereHas('workingShiftDetails', function (Builder $bl) use($working_shifts) {
                    $bl->whereIn('working_shift_id', $working_shifts);
                });
            }
        );
    }
}