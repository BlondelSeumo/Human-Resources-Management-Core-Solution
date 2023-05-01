<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Helpers\Traits\DateTimeHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Leave\Leave;
use App\Services\Tenant\Leave\LeaveCalendarService;

class LeaveLogController extends Controller
{
    use SettingKeyHelper, DateTimeHelper;

    public function __construct(LeaveCalendarService $service)
    {
        $this->service = $service;
    }

    public function index(Leave $leave): Leave
    {
        $leave = $leave->load([
            'user:id,first_name,last_name',
            'user.department.parentDepartment:id',
            'assignedBy:id,first_name,last_name',
            'type:id,name',
            'attachments',
            'lastReview',
            'lastReview.department:id,manager_id',
            'comments',
            'reviews:id,leave_id,reviewed_by,status_id,created_at,department_id',
            'reviews.department:id,department_id',
            'reviews.reviewedBy:id,first_name,last_name',
            'reviews.status:id,name',
            'reviews.comments',
            'status:id,name'
        ]);

        $leave->attendances = Attendance::query()
            ->with('details')
            ->where('user_id', $leave->user->id)
            ->whereBetween('in_date', [$this->carbon($leave->start_at)->parse()->toDateString(), $this->carbon($leave->end_at)->parse()->toDateString()])
            ->get();

        $leaveApprovalLevel = $this->getSettingFromKey('leave')('approval_level') ?: 'single';
        $leave->allowBypass = $leaveApprovalLevel == 'multi' ?
            !!$this->getSettingFromKey('leave')('allow_bypass') ?: false : false;

        if ($leave->duration_type == 'multi_day') {
            $ranges = [$this->carbon($leave->start_at)->toDate(), $this->carbon($leave->end_at)->toDate()];
            $holidays = $this->service->setRanges($ranges)->getUserHolidays($leave->user);
            $leave_days = $this->service
                ->setRanges($ranges)
                ->setEmployeeIds([$leave->user->id])
                ->buildWorkshiftService()
                ->getLeaveDate($leave, $holidays);
            $leave->leave_days = count($leave_days->filter());
        };

        return $leave;
    }
}
