<?php

namespace App\Services\Tenant\Attendance;

use App\Helpers\Core\Traits\HasWhen;
use App\Helpers\Core\Traits\Memoization;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use App\Repositories\Tenant\Holiday\HolidayRepository;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\DB;

class AttendanceSummaryService extends TenantService
{
    use Memoization, HasWhen, DateRangeHelper, DateTimeHelper;

    protected array $ranges;

    protected $holidays;

    public function setRanges(array $ranges): AttendanceSummaryService
    {
        $this->ranges = count($ranges) === 1 ? [$ranges[0], $ranges[0]] : $ranges;
        return $this;
    }


    public function getTotalScheduled()
    {
        return collect($this->dateRange($this->ranges[0], $this->ranges[1]))->map(function (SupportCarbon $date) {
            $workingShiftDetails = $this->getWorkingShiftFromDate($date);
            return $workingShiftDetails->is_weekend || $this->checkHoliday($date) ? 0 : $workingShiftDetails->getWorkingHourInSeconds();
        })->sum();
    }

    public function getTotalScheduledDays()
    {
        return collect($this->dateRange($this->ranges[0], $this->ranges[1]))->map(function (SupportCarbon $date) {
            $workingShiftDetails = $this->getWorkingShiftFromDate($date);
            return $workingShiftDetails->is_weekend || $this->checkHoliday($date) ? 0 : 1;
        })->sum();
    }

    public function getEmployeeWorkingShifts()
    {
        return WorkingShiftUser::with([
            'workingShift.details'
        ])->where('user_id', $this->model->id)
            ->where(function (Builder $builder) {
                $builder->whereBetween(DB::raw('DATE(start_date)'), $this->ranges)
                    ->whereBetween(DB::raw('DATE(end_date)'), $this->ranges)
                    ->orWhereNull('end_date');
            })->latest('start_date')
            ->get();
    }

    public function getWorkingShiftFromDate(SupportCarbon $carbon): WorkingShiftDetails
    {
        $day = $this->carbon($carbon)->toDayInLowerCase();

        $employeeWorkingShifts = $this->getEmployeeWorkingShifts();

        $employeeWorkingShift = $employeeWorkingShifts->first(function (WorkingShiftUser $workingShiftUser) use ($carbon) {
            if ($workingShiftUser->end_date) {
                return $this->carbon($carbon)->parse()->isBetween(
                    $this->carbon($workingShiftUser->start_date)->parse(),
                    $this->carbon($workingShiftUser->end_date)->parse()
                );
            }

            return $this->carbon($carbon)->parse()->isAfter($this->carbon($workingShiftUser->start_date)->parse()) ||
                $this->carbon($carbon)->parse()->isSameDay($this->carbon($workingShiftUser->start_date)->parse());
        });

        if (!$employeeWorkingShift) {
            $employeeWorkingShift = (object)['workingShift' => $this->getDefaultWorkingShift()];
        }

        /** @var WorkingShift $workingShift */
        $workingShift = $employeeWorkingShift->workingShift;

        return $workingShift->details->first(fn(WorkingShiftDetails $details) => $details->weekday === $day);
    }

    public function getDefaultWorkingShift()
    {
        return WorkingShift::getDefault(['id'])->load('details');
    }

    public function getHolidays()
    {
        return resolve(HolidayRepository::class)
            ->getDates($this->holidays);
    }

    public function setHolidays($holidays): AttendanceSummaryService
    {
        $this->holidays = $holidays;
        return $this;
    }

    public function generateEmployeeHolidaysFromDepartments(Collection $departments): \Illuminate\Support\Collection
    {
        return resolve(HolidayRepository::class)->generateHolidays($departments);
    }

    public function checkHoliday($date): bool
    {
        return collect($this->getHolidays())->contains(fn($holiday) => $date->eq($holiday));
    }
}
