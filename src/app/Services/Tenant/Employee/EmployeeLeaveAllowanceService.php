<?php

namespace App\Services\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Helpers\Traits\SettingHelper;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\LeaveType;
use App\Models\Tenant\Leave\UserLeave;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Leave\LeaveService;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeLeaveAllowanceService extends TenantService
{
    use SettingHelper;

    protected User $employee;

    public function __construct(UserLeave $userLeave)
    {
        $this->model = $userLeave;
    }

    public function setEmployee(User $employee): self
    {
        $this->employee = $employee;
        return $this;
    }

    public function buildUserLeave()
    {
        $ranges = $this->getAttr('ranges');
        $this->model = UserLeave::where('leave_type_id', $this->getAttr('leave_type_id'))
            ->where('user_id', $this->employee->id)
            ->where(function (Builder $builder) use ($ranges) {
                $builder->whereBetween(DB::raw('DATE(start_date)'), $ranges)
                    ->orWhere(function (Builder $builder) use ($ranges) {
                        $builder->whereBetween(DB::raw('DATE(end_date)'), $ranges)
                            ->orWhere(function (Builder $builder) use ($ranges) {
                                $builder->whereDate('start_date', '<', $ranges[0])
                                    ->whereDate('end_date', '>', $ranges[1]);
                            });
                    });
            })
            ->firstOrNew();
        return $this;
    }

    public function validateIfUpdateAble()
    {
        if (!$this->model->exists) {
            return $this;
        }

        $userLeave = $this->model->load($this->relations());

        $taken = $userLeave->leaveType->leaves->count() ?
            resolve(LeaveService::class)->getTakenLeaveAmount(
                $userLeave->leaveType->leaves, $this->getAttr('ranges')
            ) : 0;

        throw_if(
            (float)$this->getAttr('amount') < $taken,
            new GeneralException(__t('user_leave_cant_be_less_than_taken_amount', ['taken' => $taken]))
        );

        return $this;
    }

    public function update(): EmployeeLeaveAllowanceService
    {
        [$start_date, $end_date] = $this->getAttr('ranges');

        $this->employee->assignedLeaves()->save($this->model->fill([
            'user_id' => $this->employee->id,
            'leave_type_id' => $this->getAttr('leave_type_id'),
            'amount' => $this->getAttr('amount'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'is_updated' => $this->getAttr('is_updated') ?: 0
        ]));

        return $this;
    }

    public function relations(): array
    {
        $leaveStatuses = resolve(StatusRepository::class)->leaveApprovedPending();

        return [
            'leaveType',
            'leaveType.leaves' => $this->leaveRelations($leaveStatuses),
            'leaveType.leaves.user:id',
            'leaveType.leaves.user.departments:id',
            'leaveType.leaves.user.departments.holidays'
        ];
    }

    public function leaveRelations($leaveStatuses)
    {
        return fn(HasMany $hasMany) => $hasMany->where('user_id', $this->employee->id)
            ->select(['id', 'user_id', 'leave_type_id', 'status_id', 'start_at', 'end_at', 'duration_type'])
            ->whereBetween(DB::raw('DATE(start_at)'), $this->getAttr('ranges'))
            ->whereBetween(DB::raw('DATE(end_at)'), $this->getAttr('ranges'))
            ->whereIn('status_id', $leaveStatuses);
    }

    public function validateIfDeleteAble()
    {
        $leaveStatuses = resolve(StatusRepository::class)->leaveApprovedPending();

        $this->model->load(['leaveType.leaves' => $this->leaveRelations($leaveStatuses)]);

        throw_if(
            $this->model->leaveType->leaves->count(),
            new GeneralException(__t('cant_delete_employee_leave_type_warning'))
        );

        return $this;
    }

    public function checkAndSetMonthlyEarningAmount(LeaveType $leaveType): EmployeeLeaveAllowanceService
    {
        $this->setAttr('is_updated', $this->model->is_updated);

        if ($this->model->exists && $this->model->is_updated && $this->checkIfUpdatedThisYear()) {
            $this->setAttr('amount', $this->model->amount);
            return $this;
        }

        $earning_rate = $leaveType->amount / 12;
        $ranges = $this->leaveYear();
        $leaveStartMonth = $this->getSettingFromKey('leave')('start_month');

        if ((boolean)$leaveType->is_earning_enabled == 0) {
            if (!$this->model->exists && (nowFromApp()->month != $leaveStartMonth)) {
                $monthDifference = (nowFromApp()->diffInMonths($this->carbon($ranges[1])->parse())) + 1;
                return $this->setAttr('amount', $earning_rate * $monthDifference);
            }
            if ($this->model->exists && (nowFromApp()->month != $leaveStartMonth) && $this->checkIfStartedThisYear()) {
                $monthDifference = ($this->carbon($this->model->created_at)->parse()->diffInMonths($this->carbon($ranges[1])->parse())) + 1;
                return $this->setAttr('amount', $earning_rate * $monthDifference);
            }
            return $this->setAttr('amount', $leaveType->amount);
        }

        $start_month = $this->getStartMonth($leaveStartMonth);
        $monthDifference = ($this->carbon($ranges[0])->parse()->month($this->carbon($start_month)->parse()->month)
                ->diffInMonths(nowFromApp())) + 1;

        return $this->setAttr('amount', $earning_rate * $monthDifference);

    }

    private function checkIfStartedThisYear(): bool
    {
        return ($this->carbon($this->model->created_at)->parse()->year == $this->carbon($this->model->start_date)->parse()->year);
    }

    private function getStartMonth($leaveStartMonth): string
    {
        if ($this->model->exists && $this->checkIfStartedThisYear()) {
            return $this->carbon($this->model->created_at)->parse()->format('M');
        }
        if (!$this->model->exists && (nowFromApp()->month != $this->carbon($leaveStartMonth)->parse()->month)) {
            return nowFromApp()->format('M');
        }
        return $leaveStartMonth;
    }

    private function checkIfUpdatedThisYear(): bool
    {
        if ($this->carbon($this->model->updated_at)->parse()->year == $this->carbon($this->model->start_date)->parse()->year) {
            return true;
        }
        $this->setAttr('is_updated', 0);
        return false;
    }

}
