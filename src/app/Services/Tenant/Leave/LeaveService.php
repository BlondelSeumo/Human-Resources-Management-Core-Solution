<?php

namespace App\Services\Tenant\Leave;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class LeaveService extends TenantService
{
    use DateRangeHelper, DateTimeHelper;

    public Leave $leave;

    protected LeaveCalendarService $leaveCalendarService;

    public function __construct(LeaveCalendarService $leaveCalendarService)
    {
        $this->leaveCalendarService = $leaveCalendarService;
    }

    public function setLeave(Leave $leave): LeaveService
    {
        $this->leave = $leave;
        return $this;
    }

    /**
     * @param Collection $leaves
     * should be eager load the relational user
     * and its departmentsWhich also includes holiday as well
     * @param $ranges
     * Range parameter is for to calculate workingShift and holidays
     * @return mixed
     */
    public function getTakenLeaveAmount(Collection $leaves, array $ranges)
    {
        $workingShiftsService = $this->leaveCalendarService->setRanges($ranges)
            ->setEmployeeIds($leaves->pluck('user_id')->toArray())
            ->buildWorkshiftService();

        return $leaves->reduce(function ($count, Leave $leave) use ($workingShiftsService) {
            $holidays = $this->leaveCalendarService->getUserHolidays($leave->user);
            if (in_array($leave->duration_type, Leave::$day_duration_types)) {
                $dates = collect($this->dateRange($this->carbon($leave->start_at)->parse(), $this->carbon($leave->end_at)->parse()))
                    ->filter(fn(Carbon $carbon) => $this->checkIfItsALeaveDay(
                        $carbon,
                        $holidays,
                        $workingShiftsService->getWorkingShiftDetails($leave->user_id, $carbon)
                    ));
                return $count + count($dates);
            }

            if (in_array($leave->duration_type, Leave::$half_day_duration_types)) {
                return $count + .5;
            }

            if (in_array($leave->duration_type, Leave::$hours_duration_types)) {
                return $count + $leave->getHourPercentage(
                        $workingShiftsService->getWorkingShiftDetails($leave->user_id, $leave->start_at)
                    );
            }

        }, 0);
    }

    public function checkIfItsALeaveDay($date, array $holidays, WorkingShiftDetails $details)
    {
        return !in_array($this->carbon($date)->toDate(), $holidays) && !$details->is_weekend;
    }

}
