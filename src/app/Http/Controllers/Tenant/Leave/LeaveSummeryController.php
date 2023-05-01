<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Filters\Tenant\LeaveSummeryFilter;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\SettingHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Employee\EmployeeLeaveAllowanceController;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\Leave;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Leave\LeaveCalendarService;
use App\Services\Tenant\Leave\LeaveService;
use App\Services\Tenant\Leave\LeaveSummeryService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveSummeryController extends Controller
{
    use DateRangeHelper, SettingKeyHelper, SettingHelper;

    protected LeaveCalendarService $calendarService;

    public function __construct(LeaveSummeryService $service, LeaveSummeryFilter $filter, LeaveCalendarService $calendarService)
    {
        $this->service = $service;
        $this->filter = $filter;
        $this->calendarService = $calendarService;
    }

    public function index(User $employee): array
    {
        $ranges = $this->leaveYear();
        $employee->load('leaves:id,user_id,status_id,end_at,start_at', 'leaves.status:id,name,class');

        $allowances = resolve(EmployeeLeaveAllowanceController::class)->index($employee, true);

        $taken = $allowances['allowances']->reduce(function ($carry, $item) {
            return $carry + $item["taken"];
        }, 0);
        $total = $allowances['allowances']->reduce(function ($carry, $item) {
            return $carry + $item["amount"];
        }, 0);

        $approved = resolve(StatusRepository::class)->leaveApproved();
        $upcomingLeaves = $employee->leaves()
            ->whereBetween(DB::raw('DATE(start_at)'), $ranges)
            ->whereBetween(DB::raw('DATE(end_at)'), $ranges)
            ->where('status_id', $approved)
            ->where('start_at', '>', nowFromApp())
            ->get();
        $pending = resolve(StatusRepository::class)->leavePending();
        $pendingLeaves = $employee->leaves()
            ->whereBetween(DB::raw('DATE(start_at)'), $ranges)
            ->whereBetween(DB::raw('DATE(end_at)'), $ranges)
            ->where('status_id', $pending)
            ->count();

        $upcomingAmount = resolve(LeaveService::class)->getTakenLeaveAmount($upcomingLeaves, $ranges);
        return [
            'card_summaries' => [
                'spent' => $taken,
                'available' => $total - $taken,
                'upcoming' => $upcomingAmount,
                'pending' => $pendingLeaves,
            ],
            'employee' => $employee
        ];
    }

    public function summaries(User $employee): LengthAwarePaginator
    {
        $within = request()->get('within');
        $month = $within ?: request('month_number') + 1;
        $ranges = $this->convertRangesToStringFormat($this->getStartAndEndOf($month, request()->get('year')));
        $ranges = count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges;

        if ($within === 'thisYear') {
            $ranges = $this->leaveYear();
        }

        $leaves = $this->service
            ->filters($this->filter)
            ->where('user_id', $employee->id)
            ->with([
                'status:id,name,class',
                'type:id,name',
                'lastReview',
                'lastReview.department:id,manager_id',
                'attachments',
                'comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                    ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id'),
            ])
            ->where(function (Builder $builder) use($ranges){
                $builder->whereBetween(DB::raw('DATE(start_at)'), $ranges)
                    ->orWhereBetween(DB::raw('DATE(end_at)'), $ranges);
            })->latest('date')
            ->paginate(request()->get('per_page', 10));

        $leaves->map(function (Leave $leave) use ($ranges) {
            if ($leave->duration_type == 'multi_day') {
                $holidays = $this->calendarService->setRanges($ranges)->getUserHolidays($leave->user);
                $leave_days = $this->calendarService
                    ->setRanges($ranges)
                    ->setEmployeeIds([$leave->user->id])
                    ->buildWorkshiftService()
                    ->getLeaveDate($leave, $holidays);
                $leave->leave_days = count($leave_days->filter());
            };
        });

        return $leaves;
    }


    public function leavePeriods()
    {
        return Leave::selectRaw("DATE_FORMAT(start_at, '%Y') as year")
            ->groupBy('year')
            ->pluck('year')
            ->map(fn($year) => ['id' => $year, 'value' => $year]);
    }
}
