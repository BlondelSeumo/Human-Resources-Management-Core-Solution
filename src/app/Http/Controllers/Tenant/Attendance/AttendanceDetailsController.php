<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Filters\Tenant\AttendanceDetailsFilter;
use App\Helpers\Traits\AssignRelationshipToPaginate;
use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Holiday\Holiday;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Core\BaseRepository;
use App\Repositories\Core\Status\StatusRepository;
use App\Repositories\Tenant\Attendance\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class AttendanceDetailsController extends Controller
{
    use DateRangeHelper, AssignRelationshipToPaginate, DateTimeHelper;

    protected BaseRepository $repository;

    public function __construct(AttendanceDetailsFilter $filter, UserRepository $repository)
    {
        $this->filter = $filter;
        $this->repository = $repository;
    }

    public function index()
    {
        $within = request()->get('within');
        $month = $within ? $within : request('month_number') + 1;

        $attendanceActive = resolve(StatusRepository::class)
            ->attendanceApprove();

        $ranges = $this->getStartAndEndOf($month, request()->get('year'));

        if (request()->has('time_range') && request()->get('time_range')){
            $dateRange = json_decode(htmlspecialchars_decode(request()->get('time_range')), true);
            $ranges = [
                $this->carbon($dateRange['start'])->parse(),
                $this->carbon($dateRange['end'])->parse()
            ];
        }

        $holidays = Holiday::generalHolidays($ranges);
        $leaveApprovedStatus = resolve(StatusRepository::class)->leaveApproved();
        $paginated = $this->repository
            ->setFilter($this->filter)
            ->setRelationships([
                'department:id,name',
                'attendances' => $this->filter->detailsFilter($attendanceActive, $ranges),
                'attendances.details' => function (HasMany $details) use ($attendanceActive) {
                    $details->where('status_id', $attendanceActive)
                        ->select(['id', 'in_time', 'out_time', 'attendance_id']);
                },
                'profilePicture:id,fileable_type,fileable_id,path',
                'workingShifts:id,name',
                'workingShifts.details:id,working_shift_id,is_weekend,start_at,end_at,weekday',
                'department.holidays' => $this->filter->departmentHolidayFilter($ranges),
                'leaves' => function (HasMany $leave) use ($ranges, $leaveApprovedStatus) {
                    $leave->where('status_id', $leaveApprovedStatus)
                        ->whereHas('type', fn(Builder $leaveType) => $leaveType->where('type', 'paid'))
                        ->where(function (Builder $builder) use ($ranges){
                            $builder->whereBetween(DB::raw('DATE(start_at)'), count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges)
                                ->orWhereBetween(DB::raw('DATE(end_at)'), count($ranges) == 1 ? [$ranges[0], $ranges[0]] : $ranges);
                        });
                }
            ])->get();


        return [
            'range' => request()->has('time_range') && request()->get('time_range') ?
                $this->dateRange($ranges[0], $ranges[1])
                : $this->getDateRange($month, request()->get('year')),
            'attendances' => $this->paginated($paginated)
                ->setRelation($this->generateHolidayListFromDates($holidays))
                ->get(),
            'default' => WorkingShift::getDefault(['id', 'name'])->load('details')
        ];
    }

    public function attendancePeriods()
    {
        return Attendance::selectRaw("DATE_FORMAT(in_date, '%Y') as year")
            ->groupBy('year')
            ->pluck('year')
            ->map(fn($year) => ['id' => $year, 'value' => $year]);
    }

    public function generateHolidayListFromDates(Collection $holidays)
    {
        return function (User $user) use ($holidays) {
            $user->setAttribute(
                'holidays',
                Holiday::getDatesFromHolidays($holidays->merge(optional($user->department)->holidays ?: collect([])))
            );
        };
    }
}
