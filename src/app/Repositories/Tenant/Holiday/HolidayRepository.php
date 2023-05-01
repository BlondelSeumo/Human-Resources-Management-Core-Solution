<?php


namespace App\Repositories\Tenant\Holiday;


use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Holiday\Holiday;
use App\Repositories\Tenant\TenantRepository;
use Illuminate\Database\Eloquent\Collection;

class HolidayRepository extends TenantRepository
{
    protected array $ranges;

    public function generateHolidays(Collection $departments): \Illuminate\Support\Collection
    {
        return $departments->reduce(function ($holidays, Department $department) {
            return $holidays->merge($department->holidays);
        }, collect([]));
    }

    public function getDates($holidays)
    {
        return Holiday::getDatesFromHolidays($holidays);
    }

    public function generalHolidays()
    {
        return Holiday::generalHolidays($this->ranges);
    }

    public function setRanges(array $ranges): HolidayRepository
    {
        $this->ranges = $ranges;
        return $this;
    }
}