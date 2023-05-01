<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Filters\Tenant\LeaveRequestFilter;
use App\Helpers\Traits\SettingHelper;
use App\Helpers\Traits\UserAccessQueryHelper;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Leave\Leave;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Leave\LeaveCalendarService;
use App\Services\Tenant\Leave\LeaveRequestService;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestController extends Controller
{
    use UserAccessQueryHelper, SettingHelper;

    protected LeaveCalendarService $calendarService;

    public function __construct(LeaveRequestFilter $filter, LeaveRequestService $service, LeaveCalendarService $calendarService)
    {
        $this->filter = $filter;
        $this->service = $service;
        $this->calendarService = $calendarService;
    }

    public function index()
    {
        $leaveRequest = Leave::filters($this->filter)
            ->with($this->service->relations())
            ->latest()
            ->when(request()->has('rejected') && request()->get('rejected') == 'true',
                fn(Builder $builder) => $builder
                    ->where('status_id', resolve(StatusRepository::class)->leaveRejected()),
                fn(Builder $builder) => $builder
                    ->where('status_id', resolve(StatusRepository::class)->leavePending())
            )->paginate(request()->get('per_page', 10));

        $leaveRequest->map(function (Leave $leave) {
            if ($leave->duration_type == 'multi_day') {
                $holidays = $this->calendarService->setRanges($this->leaveYear())->getUserHolidays($leave->user);
                $leave_days = $this->calendarService
                    ->setRanges($this->leaveYear())
                    ->setEmployeeIds([$leave->user->id])
                    ->buildWorkshiftService()
                    ->getLeaveDate($leave, $holidays);
                $leave->leave_days = count($leave_days->filter());
            };
        });

        return $leaveRequest;
    }
}
