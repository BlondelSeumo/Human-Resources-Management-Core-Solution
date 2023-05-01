<?php

namespace App\Http\Controllers\Tenant\Dashboard;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\Helper\DashboardUserAccessQueryFilter;
use App\Filters\Tenant\Helper\DashboardWhereHasUserAccessQueryFilter;
use App\Filters\Tenant\Helper\DepartmentAccessFilter;
use App\Filters\Tenant\Helper\UserAccessFilter;
use App\Filters\Tenant\Helper\WhereHasEmployeesAccessFilter;
use App\Filters\Tenant\Helper\WhereHasUserAccessFilter;
use App\Filters\Tenant\Helper\WhereHasUsersAccessFilter;
use App\Helpers\Traits\UserAccessQueryHelper;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\Leave\Leave;
use App\Repositories\Core\Status\StatusRepository;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\Dashboard\AdminDashboardService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class AdminDashboardController extends Controller
{
    use UserAccessQueryHelper;
    /**
     * @var DepartmentAccessFilter
     */
    private DepartmentAccessFilter $departmentFilter;
    /**
     * @var WhereHasUserAccessFilter
     */
    private WhereHasUserAccessFilter $whereHasUserFilter;
    /**
     * @var UserAccessFilter
     */
    private UserAccessFilter $userFilter;

    private DashboardUserAccessQueryFilter $dashboardUserAccessQueryFilter;
    /**
     * @var WhereHasUsersAccessFilter
     */
    private WhereHasUsersAccessFilter $whereHasUsersAccessFilter;
    /**
     * @var WhereHasEmployeesAccessFilter
     */
    private WhereHasEmployeesAccessFilter $whereHasEmployeesAccessFilter;

    private DashboardWhereHasUserAccessQueryFilter $dashboardWhereHasUserAccessQueryFilter;

    public function __construct(
        AdminDashboardService          $service,
        UserAccessFilter               $userFilter,
        DepartmentAccessFilter         $departmentFilter,
        WhereHasUserAccessFilter       $whereHasUserFilter,
        WhereHasUsersAccessFilter      $whereHasUsersAccessFilter,
        WhereHasEmployeesAccessFilter  $whereHasEmployeesAccessFilter,
        DashboardUserAccessQueryFilter $dashboardUserAccessQueryFilter,
        DashboardWhereHasUserAccessQueryFilter $dashboardWhereHasUserAccessQueryFilter
    )
    {
        $this->service = $service;
        $this->userFilter = $userFilter;
        $this->departmentFilter = $departmentFilter;
        $this->whereHasUserFilter = $whereHasUserFilter;
        $this->whereHasUsersAccessFilter = $whereHasUsersAccessFilter;
        $this->whereHasEmployeesAccessFilter = $whereHasEmployeesAccessFilter;
        $this->dashboardUserAccessQueryFilter = $dashboardUserAccessQueryFilter;
        $this->dashboardWhereHasUserAccessQueryFilter = $dashboardWhereHasUserAccessQueryFilter;
    }

    public function summery(): array
    {
        $employees = User::query()->filters($this->dashboardUserAccessQueryFilter)
            ->where('is_in_employee', 1)->count();
        $departments = Department::query()->filters($this->departmentFilter)->count();

        [$leave_pending, $leave_approved] = resolve(StatusRepository::class)->leavePendingApproved();
        $leave_requests = Leave::query()->filters($this->dashboardWhereHasUserAccessQueryFilter)
            ->where('status_id', $leave_pending)->count();

        $on_leave_today = Leave::query()->filters($this->dashboardWhereHasUserAccessQueryFilter)
            ->where('status_id', $leave_approved)
            ->whereDate('start_at', '>=', todayFromApp())
            ->whereDate('end_at', '<=', todayFromApp())
            ->groupBy('user_id')->count();

        return [
            'total_employee' => auth()->user()->can('view_employees') ? $employees : 0,
            'total_department' => auth()->user()->can('view_departments') ? $departments : 0,
            'total_leave_request' => auth()->user()->can('view_all_leaves') ? $leave_requests : 0,
            'on_leave_today' => auth()->user()->can('view_all_leaves') ? $on_leave_today : 0,
        ];
    }

    public function onWorking(): array
    {
        $attendances = Attendance::query()->filters($this->dashboardWhereHasUserAccessQueryFilter)
            ->whereDate('in_date', '=', todayFromApp())->get();
        $attendancesStats = $attendances->countBy(fn(Attendance $attendance) => $attendance->behavior);

        return [
            'total' => $attendances->count(),
            'behaviour' => [
                'early' => Arr::get($attendancesStats, 'early') ?: 0,
                'late' => Arr::get($attendancesStats, 'late') ?: 0,
                'regular' => Arr::get($attendancesStats, 'regular') ?: 0,
            ]
        ];
    }

    public function employeeStatistics()
    {
        if (\request()->get('key') === 'by_employment_status' && auth()->user()->can('view_employment_statuses')) {
            return EmploymentStatus::filters($this->whereHasEmployeesAccessFilter)
                ->get()
                ->flatMap(function (EmploymentStatus $status) {
                return [
                    $status->name => $status->employees()
                        ->filters($this->dashboardUserAccessQueryFilter)
                        ->whereNull('end_date')
                        ->where('is_in_employee', 1)
                        ->count()
                ];
            });
        }
        if (\request()->get('key') === 'by_designation' && auth()->user()->can('view_designations')) {
            return Designation::filters($this->whereHasUsersAccessFilter)
                ->get()
                ->flatMap(function (Designation $designation) {
                return [
                    $designation->name => $designation->users()
                        ->filters($this->dashboardUserAccessQueryFilter)                        ->whereNull('end_date')
                        ->where('is_in_employee', 1)
                        ->count()
                ];
            });
        }
        if (\request()->get('key') === 'by_department' && auth()->user()->can('view_departments')) {
            return Department::filters($this->whereHasUsersAccessFilter)->get()->flatMap(function (Department $department) {
                return [$department->name => $department->users()
                    ->filters($this->dashboardUserAccessQueryFilter)                    ->whereNull('end_date')
                    ->where('is_in_employee', 1)
                    ->count()
                ];
            });
        }

        throw new GeneralException('can_not_fetch_data');

    }
}
