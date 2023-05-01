<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Filters\Tenant\LeaveStatusFilter;
use App\Filters\Traits\DateRangeFilter;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\UserAccessQueryHelper;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Leave\Leave;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Leave\LeaveCalendarService;
use App\Services\Tenant\Leave\LeaveRequestService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

class LeaveStatusSummaryController extends Controller
{
    use DateRangeHelper, DateRangeFilter, UserAccessQueryHelper;

    public function __construct(LeaveStatusFilter $filter, LeaveCalendarService $service)
    {
        $this->filter = $filter;
        $this->service = $service;
    }

    public function index()
    {
        $within = request()->get('within');
        $month = $within ? $within : request('month_number') + 1;
        $ranges = $this->getStartAndEndOf($month, request()->get('year'));
        $ranges = count($ranges) === 1 ? [$ranges[0], $ranges[0]] : $ranges;

        $statusApproved = resolve(StatusRepository::class)->leaveApproved();

        $leaves = Leave::filters($this->filter)
            ->with([
                'user:id,first_name,last_name,status_id',
                'user.department:id,name',
                'user.profilePicture',
                'user.status:id,name,class',
                'attachments',
                'comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                    ->select('id','commentable_type','commentable_id','user_id','type','comment', 'parent_id'),
                'status:id,name,class',
                'type:id,name'
            ])->where('status_id', $statusApproved)
            ->where(function (Builder $builder) use ($ranges) {
                $builder->whereBetween(DB::raw('DATE(start_at)'), $this->convertRangesToStringFormat(count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges))
                    ->orWhereBetween(DB::raw('DATE(end_at)'), $this->convertRangesToStringFormat(count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges));
            })->latest()
            ->paginate(request()->get('per_page', 10));

        $leaves->map(function (Leave $leave) use ($ranges) {
            if ($leave->duration_type == 'multi_day') {
                $holidays = $this->service->setRanges($ranges)->getUserHolidays($leave->user);
                $leave_days = $this->service
                    ->setRanges($ranges)
                    ->setEmployeeIds([$leave->user->id])
                    ->buildWorkshiftService()
                    ->getLeaveDate($leave, $holidays);
                $leave->leave_days = count($leave_days->filter());
            };
        });

        return $leaves;
    }

    public function summaries()
    {
        $within = request()->get('within');
        $month = $within ? $within : request('month_number') + 1;
        $ranges = $this->getStartAndEndOf($month, request()->get('year'));
        $ranges = count($ranges) === 1 ? [$ranges[0], $ranges[0]] : $ranges;

        $statusApproved = resolve(StatusRepository::class)->leaveApproved();

        $leaves = Leave::filters($this->filter)
            ->with([
                'status:id,class,name',
                'user:id',
                'user.departments:id',
                'user.departments.holidays' => $this->filter->departmentHolidayFilter($ranges)
            ])
            ->where('status_id', $statusApproved)
            ->where(function (Builder $builder) use ($ranges) {
                $builder->whereBetween(DB::raw('DATE(start_at)'), $this->convertRangesToStringFormat(count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges))
                    ->orWhereBetween(DB::raw('DATE(end_at)'), $this->convertRangesToStringFormat(count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges));
            })->select('*')
            ->addSelect(DB::raw("DATEDIFF(end_at,start_at)AS days"));


        $onLeave = with(clone $leaves)->distinct('user_id')->count('user_id');

        $singleDay = with(clone $leaves)->where('duration_type', 'single_day')->distinct('user_id')->count('user_id');

        $multiDay = with(clone $leaves)->where('duration_type', 'multi_day')->distinct('user_id')->count('user_id');

        $totalLeaveSeconds = $this->service
            ->setRanges($ranges)
            ->setEmployeeIds($leaves->get()->groupBy('user_id')->keys()->toArray())
            ->buildWorkshiftService()
            ->getTotalLeaveDurationInSeconds($leaves->get());

        return [
            'on_leave' => $onLeave,
            'total_hours' => $this->convertSecondsToHours($totalLeaveSeconds),
            'single_leave' => $singleDay,
            'multi_leave' => $multiDay,
        ];
    }
}
