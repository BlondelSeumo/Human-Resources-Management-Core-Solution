<?php

namespace App\Http\Controllers\Tenant\Dashboard;

use App\Filters\Tenant\AnnouncementFilter;
use App\Filters\Tenant\EmployeeDashboardFilter;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Helpers\Traits\SettingHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Employee\EmployeeLeaveAllowanceController;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Holiday\Holiday;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceSummaryService;
use App\Services\Tenant\Employee\AnnouncementService;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class EmployeeDashboardController extends Controller
{
    use SettingKeyHelper, DateTimeHelper, DateRangeHelper, SettingHelper;

    private AttendanceSummaryService $attendanceSummaryService;

    public function __construct(EmployeeDashboardFilter $filter, AttendanceSummaryService $attendanceSummaryService)
    {
        $this->filter = $filter;
        $this->attendanceSummaryService = $attendanceSummaryService;
    }

    public function employeeAttendance()
    {
        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        return Attendance::filters($this->filter)->with([
            'user:id,first_name,last_name,status_id',
            'workingShift:id,name',
            'workingShift.details' => fn(HasMany $hasMany) => $hasMany
                ->where('weekday', strtolower(todayFromApp()->format('D'))),
            'details' => fn(HasMany $hasMany) => $hasMany
                ->select('id', 'in_time', 'out_time', 'attendance_id', 'status_id')
                ->where('status_id', $attendanceApprove)
                ->orderBy('in_time', 'DESC')
        ])->where('status_id', $attendanceApprove)
            ->whereDate('in_date', todayFromApp())
            ->where('user_id', auth()->user()->id)->first();
    }

    public function employeeMonthlyAttendanceLog(): array
    {
        $ranges = $this->getStartAndEndOf('thisMonth', nowFromApp()->year);
        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();
        $attendances = Attendance::addSelect([
            'id',
            'user_id',
            'in_date',
            'worked' => AttendanceDetails::whereColumn('attendance_id', 'attendances.id')
                ->where('status_id', $attendanceApprove)
                ->selectRaw(DB::raw('CAST(SUM(TIME_TO_SEC(TIMEDIFF(out_time, in_time))) AS SIGNED) AS worked')),
        ])->with([
            'details' => fn(HasMany $hasMany) => $hasMany
                ->select('id', 'in_time', 'out_time', 'attendance_id', 'status_id')
                ->where('status_id', $attendanceApprove)
                ->orderBy('in_time', 'DESC')
        ])->where('user_id', auth()->user()->id)
            ->where('status_id', $attendanceApprove)
            ->whereBetween(DB::raw('DATE(in_date)'), $this->convertRangesToStringFormat(count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges))
            ->get();

        $totalScheduled = $this->attendanceSummaryService
            ->setModel(auth()->user())
            ->setRanges($ranges)
            ->setHolidays(
                $this->attendanceSummaryService
                    ->generateEmployeeHolidaysFromDepartments(auth()->user()->departments)
                    ->merge(Holiday::generalHolidays($ranges))
            )->getTotalScheduled();

        $totalScheduledTillNow = $this->attendanceSummaryService
            ->setModel(auth()->user())
            ->setRanges([$ranges[0], todayFromApp()])
            ->setHolidays(
                $this->attendanceSummaryService
                    ->generateEmployeeHolidaysFromDepartments(auth()->user()->departments)
                    ->merge(Holiday::generalHolidays($ranges))
            )->getTotalScheduled();

        $todayScheduled = $this->attendanceSummaryService
            ->setModel(auth()->user())
            ->setRanges([todayFromApp(), todayFromApp()])
            ->setHolidays(
                $this->attendanceSummaryService
                    ->generateEmployeeHolidaysFromDepartments(auth()->user()->departments)
                    ->merge(Holiday::generalHolidays($ranges))
            )->getTotalScheduled();

        $worked = $attendances->sum('worked');
        $shortage = $worked < $totalScheduled ? ($totalScheduled - $worked) : 0;
        $over_time = $worked > $totalScheduledTillNow ? ($worked - $totalScheduledTillNow) : 0;

        return [
            'total_scheduled' => $totalScheduled,
            'total_worked' => $worked,
            'shortage' => $shortage,
            'over_time' => $over_time,
            'today_scheduled' => $todayScheduled,
            'today_attendance' => $attendances->where('in_date', today()->toDateString())->first(),
        ];
    }

    public function employeeLeaveSummaries(): array
    {
        $ranges = $this->leaveYear();
        $employee = auth()->user();

        $allowances = resolve(EmployeeLeaveAllowanceController::class)->index($employee, true);
        $paid_allowance = $allowances['allowances']->filter(fn($leave) => $leave->leaveType->type == 'paid')->sum('amount');

        $pending = resolve(StatusRepository::class)->leavePending();
        $pending = $employee->leaves()
            ->select('end_at', 'start_at', 'leave_type_id')
            ->with('type:id,type')
            ->whereBetween(DB::raw('DATE(start_at)'), $ranges)
            ->whereBetween(DB::raw('DATE(end_at)'), $ranges)
            ->where('status_id', $pending)
            ->get();
        $paid_pending = $pending->filter(fn($leave) => $leave->type->type == 'paid')->count();

        $paid_taken = $allowances['allowances']->reduce(function ($carry, $item) {
            if ($item->leaveType->type == 'paid') {
                return $carry + $item["taken"];
            }
            return $carry;
        }, 0);
        $taken = $allowances['allowances']->reduce(function ($carry, $item) {
            return $carry + $item["taken"];
        }, 0);
        $total = $allowances['allowances']->reduce(function ($carry, $item) {
            return $carry + $item["amount"];
        }, 0);

        return [
            'pending' => $pending->count(),
            'paidPending' => $paid_pending,
            'total' => $total,
            'paidTotal' => $paid_allowance,
            'taken' => $taken,
            'paidTaken' => $paid_taken,
            'available' => $total - $taken,
        ];

    }

    public function announcements()
    {
        return resolve(AnnouncementService::class)
            ->filterForDashboard()
            ->oldest('start_date')
            ->paginate(request()->get('per_page', 5));
    }
}
