<?php


namespace App\Repositories\Tenant\Employee;


use App\Helpers\Core\Traits\Memoization;
use App\Helpers\Traits\DateTimeHelper;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use App\Repositories\Tenant\TenantRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WorkingShiftRepository extends TenantRepository
{
    use Memoization, DateTimeHelper;

    protected array $employees;

    protected Collection $usersWorkingShifts;

    public function getUserWorkingShiftFromDate($user_id, $date): WorkingShift
    {
        $workingShiftUser = $this->usersWorkingShifts->first(function (WorkingShiftUser $workingShiftUser) use ($date, $user_id) {
            return $workingShiftUser->user_id == $user_id &&
                (($this->carbon($date)->parse()->isBetween($workingShiftUser->start_date, $workingShiftUser->end_date) &&
                    $this->carbon($date)->parse()->isBefore($workingShiftUser->end_date)) ||
                    (($this->carbon($date)->parse()->isAfter($workingShiftUser->start_date) ||
                            $this->carbon($date)->parse()->isSameDay($workingShiftUser->start_date))
                        && is_null($workingShiftUser->end_date)));
        });

        return $workingShiftUser ? $workingShiftUser->workingShift : $this->defaultWorkingShift();
    }

    public function getWorkingShiftDetails(WorkingShift $workingShift, $date)
    {
        $day = $this->carbon($date)->toDayInLowerCase();

        return $this->memoize(
            'working-shift-details' . $workingShift->id . '-' . $day,
            fn() => $workingShift->details->first(fn(WorkingShiftDetails $details) => $details->weekday == $day)
        );
    }

    public function defaultWorkingShift()
    {
        return $this->memoize('default-work-shift', function () {
            return WorkingShift::getDefault(['id'])
                ->load('details:id,start_at,end_at,working_shift_id,weekday,is_weekend');
        });
    }

    public function setUsersWorkingShifts(Collection $usersWorkingShifts): self
    {
        $this->usersWorkingShifts = $usersWorkingShifts;
        return $this;
    }

    public function setEmployees(array $employees): self
    {
        $this->employees = $employees;
        return $this;
    }

    public function getEmployees(): array
    {
        return $this->employees;
    }

    public function getWorkingShiftFromRange(array $range)
    {
        return WorkingShiftUser::with('workingShift:id', 'workingShift.details:id,working_shift_id,start_at,end_at,is_weekend,weekday')
            ->whereIn('user_id', $this->getEmployees())
            ->where(function (Builder $builder) use ($range) {
                $builder->where(function (Builder $builder) use ($range) {
                    $builder->whereBetween(DB::raw('DATE(start_date)'), $range)
                        ->whereBetween(DB::raw('DATE(end_date)'), $range);
                })->orWhere(function (Builder $builder) use ($range) {
                    $builder->whereBetween(DB::raw('DATE(start_date)'), $range)
                        ->whereNull('end_date');
                });
            })
            ->get();
    }

}