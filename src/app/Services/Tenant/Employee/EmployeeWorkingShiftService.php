<?php

namespace App\Services\Tenant\Employee;

use App\Helpers\Core\Traits\Memoization;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use App\Repositories\Tenant\Employee\WorkingShiftRepository;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Collection;

class EmployeeWorkingShiftService extends TenantService
{
    use Memoization;

    protected WorkingShiftRepository $workingShiftRepository;

    protected array $ranges;

    protected Collection $usersWorkingShifts;

    protected WorkingShift $workingShift;


    public function __construct(WorkingShiftRepository $workingShiftRepository)
    {
        $this->workingShiftRepository = $workingShiftRepository;
    }

    public function getDetails($date): WorkingShiftDetails
    {
        return $this->workingShiftRepository
            ->getWorkingShiftDetails($this->getWorkingShift(), $date);
    }

    public function workingShift($employee, $date): self
    {
        $this->workingShift = $this->workingShiftRepository
            ->setUsersWorkingShifts($this->usersWorkingShifts)
            ->getUserWorkingShiftFromDate($employee, $date);

        return $this;
    }

    public function workingShifts(array $employees): self
    {
        $this->usersWorkingShifts = $this->memoize(
            'working-shifts-of-employees',
            fn() => $this->workingShiftRepository
                ->setEmployees($employees)
                ->getWorkingShiftFromRange($this->getRanges())
        );

        return $this;
    }

    public function setRanges(array $ranges): self
    {
        $this->ranges = $ranges;
        return $this;
    }

    public function getRanges(): array
    {
        return $this->ranges;
    }


    public function getWorkingShift(): WorkingShift
    {
        return $this->workingShift;
    }
}
