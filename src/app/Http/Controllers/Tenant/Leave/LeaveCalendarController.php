<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Filters\Tenant\EmployeeFilter;
use App\Filters\Tenant\LeaveCalendarFilter;
use App\Helpers\Core\Traits\Memoization;
use App\Helpers\Traits\AssignRelationshipToPaginate;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Helpers\Traits\UserAccessQueryHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\Leave;
use App\Repositories\Core\Status\StatusRepository;
use App\Repositories\Tenant\Holiday\HolidayRepository;
use App\Services\Tenant\Employee\EmployeeWorkingShiftService;
use App\Services\Tenant\Leave\LeaveCalendarService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveCalendarController extends Controller
{
    use DateRangeHelper, DateTimeHelper, Memoization, AssignRelationshipToPaginate;

    protected EmployeeWorkingShiftService $workingShiftService;

    protected HolidayRepository $holidayRepository;

    private EmployeeFilter $employeeFilter;

    public function __construct(
        LeaveCalendarFilter $filter,
        EmployeeWorkingShiftService $workingShiftService,
        HolidayRepository $holidayRepository,
        LeaveCalendarService $service,
        EmployeeFilter $employeeFilter
    )
    {
        $this->filter = $filter;
        $this->workingShiftService = $workingShiftService;
        $this->holidayRepository = $holidayRepository;
        $this->service = $service;
        $this->employeeFilter = $employeeFilter;
    }

    public function index(): array
    {
        $ranges = $this->fromWithinAndMonthNumberToRange(request()->get('within'), request('month_number'));

        [$approved, $pending] = $this->statuses();

        $leaves = Leave::filters($this->filter)
            ->with([
                'user:id',
                'user.department:id,name',
                'user.departments.holidays' => $this->filter->departmentHolidayFilter($ranges),
                'attachments:id,path,type',
                'comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                    ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id'),
            ])
            ->select(['id', 'status_id', 'duration_type', 'user_id', 'start_at', 'end_at'])
            ->where(function (Builder $builder) use($ranges){
                $builder->whereBetween(DB::raw('DATE(start_at)'), $ranges)
                    ->orWhereBetween(DB::raw('DATE(end_at)'), $ranges);
            })->whereIn('status_id', [$approved, $pending])
            ->get();

        $approvedLeaves = $leaves->where('status_id', $approved)->values();
        $pendingLeaves = $leaves->where('status_id', $pending)->values();

        $employeeOnLeaves = $approvedLeaves->groupBy('user_id')->count();

        $totalLeaveSeconds = $this->service
            ->setRanges($ranges)
            ->setEmployeeIds($approvedLeaves->groupBy('user_id')->keys()->toArray())
            ->buildWorkshiftService()
            ->getTotalLeaveDurationInSeconds($approvedLeaves);

        $approvedStats = $approvedLeaves->countBy(fn(Leave $leave) => $leave->duration_type)->toArray();

        $totalPendingRequest = $pendingLeaves->groupBy('user_id')->count();

        $pendingTotalLeaveSeconds = $this->service
            ->setRanges($ranges)
            ->setEmployeeIds($pendingLeaves->groupBy('user_id')->keys()->toArray())
            ->buildWorkshiftService()
            ->getTotalLeaveDurationInSeconds($pendingLeaves);

        $pendingStats = $pendingLeaves->countBy(fn(Leave $leave) => $leave->duration_type)->toArray();

        return [
            'on_leaves' => $employeeOnLeaves,
            'leave_hours' => $this->convertSecondsToHoursMinutes($totalLeaveSeconds),
            'approved_stats' => $approvedStats,
            'pending_request' => $totalPendingRequest,
            'request_hours' => $this->convertSecondsToHoursMinutes($pendingTotalLeaveSeconds),
            'pending_stats' => $pendingStats,
            'ranges' => $this->dateRange($this->carbon($ranges[0])->parse(), $this->carbon($ranges[1])->parse()),
        ];


    }

    public function summaries(): LengthAwarePaginator
    {
        $ranges = $this->fromWithinAndMonthNumberToRange(request()->get('within'), request('month_number'));

        [$approved] = $statuses = $this->statuses();

        $status = request()->get('pending') === "true" ? $statuses : [$approved];

        return User::filters($this->employeeFilter)
            ->whereHas('leaves', function (Builder $builder) use ($ranges, $status) {
                $builder->filters($this->filter)
                    ->where(function (Builder $builder) use($ranges){
                        $builder->whereBetween(DB::raw('DATE(start_at)'), $ranges)
                            ->orWhereBetween(DB::raw('DATE(end_at)'), $ranges);
                    })->whereIn('status_id', $status);
            })->with([
                'department:id,name',
                'profilePicture:id,fileable_type,fileable_id,path',
                'leaves' => function (HasMany $many) use ($ranges, $status) {
                    $many->filters($this->filter)
                        ->with('status:id,name')
                        ->where(function (Builder $builder) use($ranges){
                            $builder->whereBetween(DB::raw('DATE(start_at)'), $ranges)
                                ->orWhereBetween(DB::raw('DATE(end_at)'), $ranges);
                        })
                        ->whereIn('status_id', $status);
                },
                'leaves.comments' => fn(MorphMany $many) => $many->latest('id'),
                'leaves.attachments'
            ])->paginate(request()->get('per_page', 15));
    }

    public function statuses(): array
    {
        return [
            resolve(StatusRepository::class)->leaveApproved(),
            resolve(StatusRepository::class)->leavePending()
        ];
    }
}
