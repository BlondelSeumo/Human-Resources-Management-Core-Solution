<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Helpers\Traits\SettingHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\EmployeeLeaveRequest;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\UserLeave;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Employee\EmployeeLeaveAllowanceService;
use App\Services\Tenant\Leave\LeaveService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class EmployeeLeaveAllowanceController extends Controller
{
    use SettingHelper, SettingKeyHelper;

    public function __construct(EmployeeLeaveAllowanceService $service)
    {
        $this->service = $service;
    }

    public function index(User $employee, $takenOnlyApproved = false)
    {
        $ranges = $this->leaveYear();

        $start_month = $this->getSettingFromKey('leave')('start_month');

        $approved_status = resolve(StatusRepository::class)->leaveApproved();

        return [
            'start_month' => $start_month ?: 'jan',
            'allowances' => UserLeave::with(
                $this->service
                    ->setEmployee($employee)
                    ->setAttribute('ranges', $ranges)
                    ->relations()

            )->where('user_id', $employee->id)
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
                ->get()->each(function (UserLeave $userLeave) use ($ranges, $approved_status, $takenOnlyApproved) {
                    $takenOnlyApproved ?
                        $takenLeaves = $userLeave->leaveType->leaves->where('status_id', $approved_status) :
                        $takenLeaves = $userLeave->leaveType->leaves;
                    $userLeave->setAttribute(
                        'taken', $takenLeaves->count() ?
                        resolve(LeaveService::class)->getTakenLeaveAmount($takenLeaves, $ranges)
                        : 0
                    );

                    unset($userLeave->leaveType->leaves);
                })
        ];
    }

    public function update(UserLeave $userLeave, EmployeeLeaveRequest $request)
    {
        $this->service
            ->setAttrs($request->only('leave_type_id', 'amount'))
            ->setAttr('ranges', $this->leaveYear())
            ->setAttr('is_updated', true)
            ->setEmployee($userLeave->user)
            ->buildUserLeave()
            ->validateIfUpdateAble()
            ->update();

        return updated_responses('employee_leave');
    }

    public function destroy(UserLeave $userLeave)
    {
        $this->service
            ->setAttr('ranges', $this->leaveYear())
            ->setEmployee($userLeave->user)
            ->setModel($userLeave)
            ->validateIfDeleteAble()
            ->delete();

        return deleted_responses('employee_leave');
    }

    public function showUserLeave(UserLeave $userLeave): array
    {
        return $userLeave->only('id', 'amount', 'leave_type_id');
    }

}
