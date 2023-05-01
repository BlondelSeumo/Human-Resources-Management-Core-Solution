<?php

namespace App\Services\Tenant\Leave;

use App\Helpers\Core\Traits\Memoization;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\Leave;
use App\Repositories\Tenant\Holiday\HolidayRepository;
use App\Services\Tenant\Employee\EmployeeWorkingShiftService;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class LeaveCalendarService extends TenantService
{
    use Memoization, DateRangeHelper, DateTimeHelper;

    private HolidayRepository $holidayRepository;

    private EmployeeWorkingShiftService $workingShiftService;

    private array $ranges;

    private array $employeeIds;

    public function __construct(HolidayRepository $holidayRepository, EmployeeWorkingShiftService $workingShiftService)
    {
        $this->holidayRepository = $holidayRepository;
        $this->workingShiftService = $workingShiftService;
    }

    public function getTotalLeaveDurationInSeconds(Collection $leaves): int
    {
        return $leaves->reduce(
            fn($total, Leave $leave) => $total + $this->totalLeaveDuration(
                    $leave,
                    $this->getUserHolidays($leave->user)
                ),
            0
        );
    }

    public function getTotalLeaveDurationInDays(Collection $leaves)
    {
        return $leaves->map(function ($leave) {
            return $this->getLeaveDate($leave, $this->getUserHolidays($leave->user));
        })->flatten()->filter(fn($leave) => $leave)->toArray();
    }

    public function getLeaveDate($leave, $holidays)
    {
        $ranges = $this->ranges;

        return collect($this->dateRange(
            $this->carbon($leave->start_at)->parse(),
            $this->carbon($leave->end_at)->parse()
        ))->map(function (Carbon $carbon) use ($leave, $holidays, $ranges) {
            if (in_array($this->carbon($carbon)->toDate(), $holidays) ||
                !$carbon->between($ranges[0],$ranges[1])) {
                return null;
            }

            $workingShiftDetails = $this->getWorkingShiftDetails($leave->user_id, $carbon);

            return $workingShiftDetails->is_weekend ? null : $carbon->toDateString();
        });
    }

    public function getUserHolidays(User $user)
    {
        return $this->memoize(
            'holidays-'.$user->id,
            fn() => $this->convertRangesToStringFormat($this->holidayRepository->getDates(
                $this->holidayRepository
                    ->generateHolidays($user->departments)
                    ->merge($this->generalHolidays())
            ))
        );
    }

    public function generalHolidays()
    {
        return $this->memoize(
            'general-holidays',
            fn() => $this->holidayRepository
                ->setRanges($this->ranges)
                ->generalHolidays()
        );
    }

    public function buildWorkshiftService(): self
    {
        $this->workingShiftService->setRanges($this->ranges)->workingShifts($this->employeeIds);

        return $this;
    }

    public function totalLeaveDuration(Leave $leave, array $holidays): int
    {
        $ranges = $this->ranges;

        return collect($this->dateRange(
            $this->carbon($leave->start_at)->parse(),
            $this->carbon($leave->end_at)->parse()
        ))->reduce(function ($total, Carbon $carbon) use ($leave, $holidays, $ranges) {
            return $total + $this->getLeaveTotalInSecond($leave, $carbon, $holidays, $ranges);
        }, 0);
    }

    public function getLeaveTotalInSecond($leave, Carbon $carbon, $holidays, $ranges): int
    {
        if (in_array($this->carbon($carbon)->toDate(), $holidays) ||
            !$carbon->between($ranges[0],$ranges[1])) {
            return 0;
        }

        if (in_array($leave->duration_type, Leave::$day_duration_types)) {
            $workingShift = $this->getWorkingShiftDetails($leave->user_id, $carbon);
            return  $workingShift->is_weekend ? 0 :  $workingShift->getWorkingHourInSeconds();
        }

        return $this->carbon($leave->start_at)->parse()->diffInSeconds($leave->end_at);
    }

    public function getWorkingShiftDetails($user_id, $date)
    {
        return $this->workingShiftService
            ->workingShift($user_id, $this->carbon($date)->toDate())
            ->getDetails($this->carbon($date)->toDate());
    }

    public function setRanges(array $ranges): self
    {
        $this->ranges = $ranges;
        return $this;
    }


    public function setEmployeeIds(array $employeeIds): self
    {
        $this->employeeIds = $employeeIds;
        return $this;
    }


}
