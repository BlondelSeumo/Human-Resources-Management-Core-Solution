<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Filters\Common\Auth\UserFilter;
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
use App\Services\Tenant\Leave\LeaveService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceSummaryController extends Controller
{
    use DateRangeHelper, SettingKeyHelper;

    private AttendanceDetailsFilter $detailsFilter;

    private LeaveCalendarService $leaveCalendarService;

    private UserAccessFilter $userAccessFilter;

    public function __construct(
        AttendanceSummaryFilter $filter,
        AttendanceDetailsFilter $detailsFilter,
        AttendanceSummaryService $service,
        LeaveCalendarService $leaveCalendarService,
        UserAccessFilter $userAccessFilter
    )
    {
        $this->filter = $filter;
        $this->detailsFilter = $detailsFilter;
        $this->service = $service;
        $this->leaveCalendarService = $leaveCalendarService;
        $this->userAccessFilter = $userAccessFilter;
    }

    public function index(User $employee)
    {
        $within = request()->get('within');
        $month = $within ? $within : request('month_number') + 1;
        $ranges = $this->getStartAndEndOf($month, request()->get('year'));
        $ranges = count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges;

        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        $employee = $employee->load([
            'profilePicture',
            'status:id,name,class',
            'department:id,name',
            'departments:id',
            'departments.holidays' => $this->detailsFilter->departmentHolidayFilter($ranges),
            'profile:id,user_id,joining_date,employee_id',
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

        $taken = resolve(LeaveService::class)->getTakenLeaveAmount($employee->leaves, $ranges);

//        if (optional($employee->profile)->joining_date && $within != 'today') {
//            /** @var Carbon $start */
//            $start = $ranges[0];
//            if ($start->isBefore(Carbon::parse(optional($employee->profile)->joining_date))) {
//                $ranges[0] = Carbon::parse(optional($employee->profile)->joining_date);
//            }
//        }

        $attendances = Attendance::addSelect([
            'id',
            'user_id',
            'behavior',
            'worked' => AttendanceDetails::whereColumn('attendance_id', 'attendances.id')
                ->where('status_id', $attendanceApprove)
                ->selectRaw(DB::raw('CAST(SUM(TIME_TO_SEC(TIMEDIFF(out_time, in_time))) AS SIGNED) AS worked')),
        ])->where('user_id', $employee->id)
            ->where('status_id', $attendanceApprove)
            ->whereBetween(DB::raw('DATE(in_date)'), $this->convertRangesToStringFormat($ranges))
            ->get();

        $behaviors = $attendances->countBy('behavior');

        $worked = $attendances->sum('worked');

        $average = $behaviors->keys()->first(fn($key) => $behaviors[$key] == $behaviors->max());

        $totalScheduled = $this->service
            ->setModel($employee)
            ->setRanges($ranges)
            ->setHolidays(
                $this->service
                    ->generateEmployeeHolidaysFromDepartments($employee->departments)
                    ->merge(Holiday::generalHolidays($ranges))
            )->getTotalScheduled();

        $behaviors = $behaviors->toArray();


        $behaviors = [
            'regular' => Arr::get($behaviors, 'regular') ?: 0,
            'early' => Arr::get($behaviors, 'early') ?: 0,
            'late' => Arr::get($behaviors, 'late') ?: 0,
            'on_leave' => $taken,
        ];

        $balance = ($worked + $paidLeave) - $totalScheduled;

        $setting = $this->getSettingFromKey('attendance')('work_availability_definition');

        $average = $average ? $average : 'absent';

        $average_class = config('settings.attendance_behavior')[$average];

        $percentage = $this->generatePercentage($totalScheduled, ($worked + $paidLeave));

        $availability_behavior = $percentage >= $setting ? __t('good') : __t('bad');
        $availability_behavior_class = $percentage >= $setting ? 'success' : 'danger';

        return array_merge([
            'worked' => $this->convertSecondsToHoursMinutes($worked),
            'scheduled' => $this->convertSecondsToHoursMinutes($totalScheduled),
            'average' => $average,
            'percentage' => $percentage,
            'availability_behavior' => $availability_behavior,
            'availability_behavior_class' => $availability_behavior_class,
            'balance' => $this->convertSecondsToHoursMinutes($balance),
            'balance_behavior' => $balance < 0 ? __t('lack') : __t('extra'),
            'average_class' => $average_class,
            'paid_leave' => $this->convertSecondsToHoursMinutes($paidLeave),
            'employee' => $employee
        ], $behaviors);

    }

    public function generatePercentage($totalScheduled, $worked)
    {
        if ($totalScheduled) {
            return number_format($worked * 100 / $totalScheduled, 2);
        }

        return number_format($worked ? ($worked * 100) / $worked : 0, 2);
    }

    public function summaries(User $employee)
    {
        $within = request()->get('within');
        $month = $within ? $within : request('month_number') + 1;
        $ranges = $this->getStartAndEndOf($month, request()->get('year'));

        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        return Attendance::filters($this->filter)
            ->select(['id', 'in_date', 'user_id', 'behavior'])
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
                    )
                    ->orderBy('in_time', 'DESC')
                    ->where('status_id', $attendanceApprove),
                'details.comments' => fn(MorphMany $morphMany) => $morphMany->orderBy('parent_id', 'DESC')
                    ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id')
            ])
            ->whereBetween(DB::raw('DATE(in_date)'), $this->convertRangesToStringFormat(count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges))
            ->latest('in_date')
            ->paginate(request()->get('per_page', 15));
    }

    public function users($user)
    {
        $userInvited = resolve(StatusRepository::class)->userInvited();
        return (new UserFilter(
            User::filters($this->userAccessFilter)
                ->with('profilePicture', 'status:id,name,class')
                ->where('status_id', '!=', $userInvited)
        ))->filter()
            ->get();
    }

}
