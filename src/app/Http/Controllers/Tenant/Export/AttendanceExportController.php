<?php

namespace App\Http\Controllers\Tenant\Export;

use App\Export\AllEmployeeAttendanceExport;
use App\Export\AttendanceExport;
use App\Filters\Tenant\AttendanceDetailsFilter;
use App\Filters\Tenant\AttendanceSummaryFilter;
use App\Filters\Tenant\Helper\UserAccessFilter;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Holiday\Holiday;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceSummaryService;
use App\Services\Tenant\Leave\LeaveCalendarService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

class AttendanceExportController extends Controller
{
    use DateRangeHelper, SettingKeyHelper;

    public UserAccessFilter $accessFilter;
    private LeaveCalendarService $leaveCalendarService;
    private AttendanceDetailsFilter $detailsFilter;

    public function __construct(
        AttendanceSummaryFilter $filter,
        AttendanceDetailsFilter $detailsFilter,
        UserAccessFilter $accessFilter,
        LeaveCalendarService $leaveCalendarService,
        AttendanceSummaryService $service
    )
    {
        $this->filter = $filter;
        $this->accessFilter = $accessFilter;
        $this->leaveCalendarService = $leaveCalendarService;
        $this->service = $service;
        $this->detailsFilter = $detailsFilter;
    }

    public function exportEmployeeAttendance(User $employee)
    {

        $within = request()->get('within');
        $month = $within ?: request('month_number') + 1;
        $ranges = $this->getStartAndEndOf($month, request()->get('year'));

        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        $attendances = Attendance::filters($this->filter)
            ->select(['id', 'in_date', 'user_id', 'behavior'])
            ->addSelect([
                'worked' => AttendanceDetails::whereColumn('attendance_id', 'attendances.id')
                    ->where('status_id', $attendanceApprove)
                    ->selectRaw(DB::raw('CAST(SUM(TIME_TO_SEC(TIMEDIFF(out_time, in_time))) AS SIGNED) AS worked')),
            ])
            ->where('user_id', $employee->id)
            ->where('status_id', $attendanceApprove)
            ->with([
                'details' => fn(HasMany $hasMany) => $hasMany
                    ->select(
                        'id',
                        'in_time',
                        'out_time',
                        'attendance_id',
                        'status_id',
                        'review_by',
                        'added_by',
                        'attendance_details_id',
                        'in_ip_data',
                        'out_ip_data'
                    )->orderBy('in_time', 'DESC')
                    ->where('status_id', $attendanceApprove),
                'details.comments' => fn(MorphMany $morphMany) => $morphMany->orderBy('parent_id', 'DESC')
                    ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id')
            ])
            ->whereBetween(DB::raw('DATE(in_date)'), $this->convertRangesToStringFormat(count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges))
            ->get();

        $employee = $employee->load([
            'departments:id',
            'departments.holidays' => $this->detailsFilter->departmentHolidayFilter($ranges),
            'leaves' => function (HasMany $leave) use($ranges) {
                $leave->where('status_id', resolve(StatusRepository::class)->leaveApproved())
                    ->whereHas('type', fn (Builder $leaveType) => $leaveType->where('type', 'paid'))
                    ->where(function (Builder $builder) use ($ranges){
                        $builder->whereBetween(DB::raw('DATE(start_at)'), $ranges)
                            ->orWhereBetween(DB::raw('DATE(end_at)'), $ranges);
                    });

            }
        ]);
        $paidLeave = $this->leaveCalendarService
            ->setRanges($ranges)
            ->setEmployeeIds([$employee->id])
            ->buildWorkshiftService()
            ->getTotalLeaveDurationInSeconds($employee->leaves);

        $totalScheduled = $this->service
            ->setModel($employee)
            ->setRanges($ranges)
            ->setHolidays(
                $this->service
                    ->generateEmployeeHolidaysFromDepartments($employee->departments)
                    ->merge(Holiday::generalHolidays($ranges))
            )->getTotalScheduled();

        $worked = $attendances->sum('worked');

        $balance = ($worked + $paidLeave) - $totalScheduled;


        $summery = [
            'total_scheduled' => $totalScheduled,
            'paid_leave' => $paidLeave,
            'total_worked' => $worked,
            'worked_in_hour' => round(($worked / 3600),2),
            'balance' => $balance,
            'balance_in_hour' => round(($balance / 3600),2),
        ];


        $file_name = Str::of($employee->full_name)->kebab() . '-attendances-' . Str::of($within ?: \request('month'))->kebab() . '.xlsx';

        return (new AttendanceExport($attendances, false, $summery))->download($file_name, Excel::XLSX);

    }

    public function exportAllEmployeeAttendance()
    {

        $within = request()->get('within');
        $month = $within ?: request('month_number') + 1;
        $ranges = $this->getStartAndEndOf($month, request()->get('year'));

        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        $users = User::filters($this->accessFilter)
            ->select(['id', 'first_name', 'last_name'])
            ->whereHas('attendances', $this->filter->rangeFilter($attendanceApprove, $ranges))
            ->with([
                'attendances' => function (HasMany $builder) use ($attendanceApprove, $ranges) {
                    $builder->select(['id', 'in_date', 'user_id', 'behavior']);
                    if (count($ranges) == 1) {
                        return $builder->whereDate('in_date', $ranges[0])
                            ->where('status_id', $attendanceApprove);
                    }
                    return $builder->whereDate('in_date', '>=', $ranges[0])
                        ->where('status_id', $attendanceApprove)
                        ->whereHas(
                            'details',
                            fn(Builder $bl) => $bl->whereDate('out_time', '<=', $ranges[1])
                                ->where('status_id', $attendanceApprove)
                        );
                },
                'attendances.details' => function (HasMany $details) use ($attendanceApprove) {
                    $details->where('status_id', $attendanceApprove)
                        ->orderBy('in_time', 'DESC')
                        ->select([
                            'id',
                            'in_time',
                            'out_time',
                            'attendance_id',
                            'status_id',
                            'review_by',
                            'added_by',
                            'attendance_details_id',
                            'in_ip_data',
                            'out_ip_data'
                        ]);
                },
                'attendances.details.comments' => fn(MorphMany $morphMany) => $morphMany->orderBy('parent_id', 'DESC')
                    ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id')
            ])->get();

        $file_name = 'all-employees-attendances-' . Str::of($within ?: \request('month'))->kebab() . '.xlsx';

        return (new AllEmployeeAttendanceExport($users))->download($file_name, Excel::XLSX);

    }

    public function exportDailyLogAttendance()
    {
        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        $attendances = Attendance::filters($this->filter)
            ->with([
                'user:id,first_name,last_name,status_id',
                'user.department:id,name',
                'details.status:name,id',
                'details' => fn(HasMany $hasMany) => $hasMany
                    ->select(
                        'id',
                        'in_time',
                        'out_time',
                        'attendance_id',
                        'status_id',
                        'review_by',
                        'added_by',
                        'attendance_details_id',
                        'in_ip_data',
                        'out_ip_data'
                    )
                    ->where('status_id', $attendanceApprove)
                    ->orderBy('in_time', 'DESC'),
                'details.comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                    ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id')
            ])->where('status_id', $attendanceApprove)
            ->get();

        $file_name = \request('date') . '-attendances.xlsx';

        return (new AttendanceExport($attendances, true))->download($file_name, Excel::XLSX);
    }
}
