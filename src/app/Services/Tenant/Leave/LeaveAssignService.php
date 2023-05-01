<?php

namespace App\Services\Tenant\Leave;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\FileHandler;
use App\Helpers\Core\Traits\HasWhen;
use App\Helpers\Core\Traits\Memoization;
use App\Helpers\Traits\AutoApprovalTrait;
use App\Helpers\Traits\DateTimeHelper;
use App\Helpers\Traits\SettingHelper;
use App\Models\Core\Auth\User;
use App\Models\Core\File\File;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Holiday\Holiday;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Leave\LeaveStatus;
use App\Models\Tenant\Leave\UserLeave;
use App\Models\Tenant\Utility\Comment;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use App\Notifications\Tenant\LeaveNotification;
use App\Repositories\Core\Status\StatusRepository;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveAssignService extends TenantService
{
    use HasWhen, Memoization, DateTimeHelper, SettingHelper, AutoApprovalTrait, FileHandler;

    private LeaveCalendarService $leaveCalendarService;

    public Leave $leave;

    protected Department $department;

    protected bool $needToAddReview = false;

    public string $durationType;

    protected array $attrRules = [
        'multi_day' => ['start_date' => 'required|before:end_date', 'end_date' => 'required|after:start_date'],
        'single_day' => ['date' => 'required|date'],
        'first_half' => ['date' => 'required|date'],
        'last_half' => ['date' => 'required|date'],
        'hours' => [
            'date' => 'required|date',
            'start_time' => 'required|before:end_time',
            'end_time' => 'required|after:start_time'
        ]
    ];

    public function __construct(LeaveCalendarService $leaveCalendarService)
    {
        $this->leaveCalendarService = $leaveCalendarService;
    }

    public function validateDurationType(): self
    {
        throw_if(
            !in_array($this->durationType, Leave::$duration_types),
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

    public function basicValidations(): self
    {
        validator($this->getAttributes(), [
            'employee_id' => 'required',
            'leave_duration' => 'required',
            'leave_type_id' => 'required',
            'note' => 'required',
            'attachments.*' => 'mimes:jpeg,jpg,gif,png,pdf,zip|max:2000'
        ],[
                'attachments.*.mimes' => 'Only jpeg, jpg, gif, png, pdf and zip files are allowed. ',
                'attachments.*.max' => 'Maximum allowed size for an file is 2MB.',
            ]
        )->validate();

        return $this;
    }

    public function setDurationType($durationType): LeaveAssignService
    {
        $this->durationType = $durationType;
        return $this;
    }

    public function validateAttributes(): self
    {
        validator($this->getAttributes(),
            $this->attrRules[$this->durationType]
        )->validate();
        return $this;
    }

    public function validTimeWithWorkingShift(): self
    {
        $workingShiftTime = $this->getWorkingShiftTime();

        throw_if(
            !$workingShiftTime->start_at,
            new GeneralException(__t('leave_is_not_available_on_weekend'))
        );

        $startTime = $this->carbon($this->getAttr('start_time'))->toTime();
        $endTime = $this->carbon($this->getAttr('end_time'))->toTime();

        $conditions = $workingShiftTime->start_at > $workingShiftTime->end_at ?
            ($workingShiftTime->start_at <= $startTime || $workingShiftTime->end_at >= $startTime) &&
            ($workingShiftTime->start_at <= $endTime || $workingShiftTime->end_at >= $endTime) :
            ($workingShiftTime->start_at <= $startTime && $workingShiftTime->end_at >= $endTime);

        throw_if(
            !$conditions,
            new GeneralException(__t('time_not_match_with_work_shift'))
        );

        $this->setAttr('start_at', $this->carbon($this->getAttr('start_time'))->parse()->toDateTimeString());
        $this->setAttr('end_at', $this->carbon($this->getAttr('end_time'))->parse()->toDateTimeString());

        return $this;
    }

    public function getWorkingShiftTime($dayName = null)
    {
        $workingShift = $this->getWorkingShift();
        $dayName = $dayName ?: $this->carbon($this->getStartDate())->toDayInLowerCase();

        return $this->memoize('working-shift-details-'.$dayName.'-'.$this->model->id, function () use ($workingShift, $dayName) {
            return $workingShift->details()->where('weekday', $dayName)->first();
        }, $this->refreshMemoization);
    }

    public function getStartDate(): Carbon
    {
        return $this->carbon($this->getAttr($this->durationType == 'multi_day' ? 'start_date' : 'date'))->parse();
    }

    public function getEndDate(): Carbon
    {
        return $this->carbon($this->getAttr($this->durationType == 'multi_day' ? 'end_date' : 'date'))->parse();
    }


    public function getWorkingShift()
    {
        return $this->memoize('working-shift-' . $this->model->id, function () {
            $userWorkShift = WorkingShiftUser::query()
                ->where('user_id', $this->model->id)
                ->where(function (Builder $builder){
                   $builder->where(function (Builder $builder){
                       $builder->whereDate('start_date', '<=', $this->getStartDate())
                           ->whereDate('end_date', '>', $this->getStartDate());
                   })->orWhere(function (Builder $builder){
                       $builder->whereDate('start_date', '<=', $this->getStartDate())
                           ->where('end_date', null);
                   });
                })->first();

            if ($userWorkShift) {
                $workShift = WorkingShift::query()->find($userWorkShift->working_shift_id);
            }else{
                $workShift = WorkingShift::getDefault(['id']);
            }

            return $workShift;
        }, $this->refreshMemoization);
    }

    public function setWorkingShiftTime(): self
    {
        $workingShiftTime = $this->getWorkingShiftTime();

        throw_if(
            $workingShiftTime->is_weekend,
            new GeneralException(__t('leave_is_not_available_on_weekend'))
        );

        $workingShiftStartAt = $this->carbon($workingShiftTime->start_at)->parse();
        $workingShiftEndAt = $this->carbon($workingShiftTime->end_at)->parse();

        if ($workingShiftStartAt->isAfter($workingShiftEndAt)) {
            $this->bindWithWorkShiftTimeAndSet('end_at', $this->getEndDate()->addDay(), $workingShiftEndAt);
        } else {
            $this->bindWithWorkShiftTimeAndSet('end_at', $this->getEndDate(), $workingShiftEndAt);
        }
        $this->bindWithWorkShiftTimeAndSet('start_at', $this->getStartDate(), $workingShiftStartAt);

        if ($this->durationType == 'first_half') {
            $this->setDateToMid('end_at');
        }

        if ($this->durationType == 'last_half') {
            $this->setDateToMid('start_at');
        }

        return $this;
    }

    public function bindWithWorkShiftTimeAndSet($key, Carbon $date, Carbon $time): self
    {
        $this->setAttr($key, $date->toDateString() . ' ' . $time->toTimeString());

        return $this;
    }

    public function setDateToMid($key): self
    {
        $average = $this->carbon($this->getAttr('start_at'))->parse()
            ->average($this->carbon($this->getAttr('end_at'))->parse());

        $this->setAttr($key, $average->toDateTimeString());

        return $this;
    }

    public function userAvailableLeavesCheck(): self
    {
        $statuses = resolve(StatusRepository::class)->leaveApprovedPending();

        $ranges = $this->leaveYear();
        $leaves = Leave::query()->where('user_id', $this->model->id)
            ->where('leave_type_id', $this->getAttr('leave_type_id'))
            ->whereIn('status_id', $statuses)
            ->whereDate('start_at', '>=', $ranges[0])
            ->whereDate('end_at', '<=', $ranges[1])
            ->get();

        $requestedAmount = $this->getRequestedAmount();
        $amount = $this->getLeaveAmount();
        $taken = resolve(LeaveService::class)->getTakenLeaveAmount($leaves, $ranges);
        $available = $amount - $taken;

        throw_if(
            $requestedAmount > $available,
            new GeneralException(__t('requested_amount_is_greater_then_available_leaves'))
        );

        return $this;
    }

    public function validateLeaveYear(): self
    {
        $range = $this->leaveYear();
        $start_date = $this->carbon($this->getAttr('start_at'))->toDate();
        $end_date = $this->carbon($this->getAttr('end_at'))->toDate();
        throw_if(
            $this->carbon($start_date)->parse()->isBefore($range[0])
            || $this->carbon($end_date)->parse()->isAfter($range[1]),
            new GeneralException(__t('leave_must_assigned_in_current_leave_year'))
        );
        return $this;
    }

    public function getRequestedAmount()
    {
        if (in_array($this->durationType, Leave::$multi_day_duration_types)) {
            return $this->amountCountForMultiDay();
        }

        if (in_array($this->durationType, Leave::$half_day_duration_types)) {
            return 0.5;
        }

        if (in_array($this->durationType, Leave::$hours_duration_types)) {
            return $this->amountCountForHours();
        }

        return 1;
    }

    public function amountCountForHours()
    {
        $workShiftHour = $this->getWorkingShiftTime()->getWorkingHourInSeconds();
        $start_at = $this->carbon($this->getAttr('start_at'))->parse();
        $end_at = $this->carbon($this->getAttr('end_at'))->parse();

        $requestedAmount = ($start_at->diffInSeconds($end_at) / $workShiftHour);

        throw_if(
            $requestedAmount > 1,
            new GeneralException(__t('invalid_date_or_not_match_with_working_shift'))
        );

        return $requestedAmount;
    }

    public function amountCountForMultiDay()
    {
        $ranges = [
            $this->carbon($this->getAttr('start_at'))->toDate(),
            $this->carbon($this->getAttr('end_at'))->toDate(),
        ];

        $holidays = $this->leaveCalendarService->setRanges($ranges)->getUserHolidays($this->model);

        $dates = collect($this->dateRange($this->carbon($this->getAttr('start_at'))->parse(),
            $this->carbon($this->getAttr('end_at'))->parse()))
            ->filter(fn(Carbon $carbon) => $this->checkIfItsALeaveDay(
                $carbon,
                $holidays,
                $this->getWorkingShiftTime($this->carbon($carbon)->toDayInLowerCase())
            ));
        return count($dates);
    }

    public function checkIfItsALeaveDay($date, array $holidays, WorkingShiftDetails $details)
    {
        return !in_array($this->carbon($date)->toDate(), $holidays) && !$details->is_weekend;
    }

    public function getLeaveAmount()
    {
        $ranges = $this->leaveYear();
        $userLeaves = UserLeave::query()
            ->where('user_id', $this->model->id)
            ->where(function (Builder $builder) use ($ranges) {
                $builder->whereBetween(DB::raw('DATE(start_date)'), $ranges)
                    ->orWhere(function (Builder $builder) use ($ranges) {
                        $builder->whereBetween(DB::raw('DATE(end_date)'), $ranges)
                            ->orWhere(function (Builder $builder) use ($ranges) {
                                $builder->whereDate('start_date', '<', $ranges[0])
                                    ->whereDate('end_date', '>', $ranges[1]);
                            });
                    });
            })
            ->where('leave_type_id', $this->getAttr('leave_type_id'))
            ->select('amount')
            ->first();

        throw_if(
            !$userLeaves,
            new GeneralException(__t('leave_is_not_available'))
        );

        return $userLeaves->amount;
    }

    public function validateWithExistingLeaves(): self
    {
        $statuses = resolve(StatusRepository::class)->leaveApprovedPending();

        $period = [
            'start' => $this->carbon($this->getAttr('start_at'))->parse()->toDateTimeString(),
            'end' => $this->carbon($this->getAttr('end_at'))->parse()->toDateTimeString(),
        ];

        $userLeaves = Leave::query()->where('user_id', $this->model->id)
            ->with('status')
            ->where(fn(Builder $builder) => $builder
                ->whereBetween('start_at', array_values($period))
                ->orWhereBetween('end_at', array_values($period))
                ->orWhere(function ($query) use ($period) {
                    $query->whereDate('start_at', '<=', $period['start'])
                        ->whereDate('end_at', '>=', $period['end']);
                })
            )->whereIn('status_id', $statuses)
            ->first();

        if (!!$userLeaves){
            throw new GeneralException(__t($userLeaves->status->name == 'status_pending' ?
                'Leave_time_exists_in_previous_leaves_request' : 'Leave_time_exists_in_previous_leaves'));
        }

        return $this;
    }

    public function validateWithHolidays(): self
    {
        $holiday = Holiday::query()
            ->whereHas('departments',
                fn(Builder $builder) => $builder->where('id', $this->model->department->id)
            )->whereDate('start_date', '<=', $this->getAttr('start_at'))
            ->whereDate('end_date', '>=', $this->getAttr('end_at'))
            ->exists();

        throw_if(
            $holiday,
            new GeneralException(__t('cannt_add_Leaves_on_holiday'))
        );

        return $this;
    }

    public function hasApproval(): bool
    {
        if (auth()->user()->isAppAdmin()) {
            return true;
        }

        $settings = (object)$this->getSettingFromKeys('leave')('approval_level', 'users', 'roles');

        if ($this->autoSettingsApproval($settings, null, 'leave')) {
            return true;
        }

        if ($this->model->id == auth()->id()) {
            return false;
        }

        return request('access_behavior') == 'all_departments' || $this->isDepartmentManager();
    }

    public function isDepartmentManager(): bool
    {
        $leaveApprovalLevel = $this->getSettingFromKey('leave')('approval_level') ?: 'single';
        $managerDept = resolve(DepartmentRepository::class)->getDepartments(auth()->id());

        $this->department = new Department();

        if ($leaveApprovalLevel == 'multi') {
            $parentDept = resolve(DepartmentRepository::class)->getDepartments($this->model->department->id, 'parents', 'department');

            foreach (array_reverse($parentDept) as $dept) {
                if (in_array($dept, $managerDept)) {
                    $this->department = Department::find($dept)->load('manager', 'parentDepartment');
                    break;
                }
            }

            if (!!$this->department->id && $this->department->parentDepartment && $this->department->parentDepartment->id) {
                $this->needToAddReview = true;
                return false;
            }
        }

//        if (in_array($this->model->department->id, $managerDept)){
//            $this->needToAddReview = true;
//            return true;
//        }

        return in_array($this->model->department->id, $managerDept);
    }

    public function addLeaveReview(): self
    {
        $statusApproved = resolve(StatusRepository::class)->leaveApproved();

        $attributes = [
            'status_id' => $statusApproved,
            'reviewed_by' => auth()->id(),
            'department_id' => optional($this->department->parentDepartment)->id
        ];

        $this->leave->reviews()->save(new LeaveStatus($attributes));

        return $this;
    }

    public function assignLeave(): self
    {
        $this->buildNewLeaves()
            ->saveLeaves()
            ->when($this->needToAddReview,
                fn(LeaveAssignService $service) => $service->addLeaveReview()
            )->saveReason()
            ->saveAttachments();

        return $this;
    }

    public function buildNewLeaves(): self
    {
        $status = $this->getStatus();

        $this->leave = new Leave([
            'status_id' => $status,
            'working_shift_details_id' => $this->getWorkingShiftTime()->id,
            'date' => $this->getStartDate()->toDateString(),
            'start_at' => $this->getAttr('start_at'),
            'end_at' => $this->getAttr('end_at'),
            'leave_type_id' => $this->getAttr('leave_type_id'),
            'duration_type' => $this->durationType,
            'assigned_by' => $this->getAttr('assigned_by'),
            'tenant_id' => $this->getAttr('tenant_id'),
        ]);

        return $this;
    }

    public function getStatus()
    {
        $method = 'leavePending';

        if ($this->hasApproval()) {
            $method = 'leaveApproved';
        }

        return resolve(StatusRepository::class)->$method();
    }

    public function saveLeaves(): self
    {
        $this->model->leaves()->save($this->leave);

        return $this;
    }

    public function saveReason(): self
    {
        $this->leave->comments()->save(new Comment([
            'user_id' => $this->model->id,
            'type' => 'reason-note',
            'comment' => $this->getAttr('note'),
        ]));

        return $this;
    }

    public function saveAttachments(): self
    {
        if ($this->getAttr('attachments') && count($this->getAttr('attachments')) > 0) {
            foreach ($this->getAttr('attachments') as $attachment) {
                $this->leave->attachments()->save(new File([
                    'path' => $this->storeFile($attachment, 'expense'),
                    'type' => 'leave-attachment',
                ]));
            }
        }

        return $this;
    }

    public function checkPermissionsAndAssigner(): self
    {
        $this->when(
            auth()->id() != $this->model->id,
            fn(LeaveAssignService $service) => $service->when(
                auth()->user()->can('assign_leaves'),
                fn(LeaveAssignService $service) => $service->setAttr('assigned_by', auth()->id()),
                new GeneralException(__t('action_not_allowed'))
            )
        );

        return $this;
    }

    public function sendNotification(Leave $leave): self
    {
        $events = [
            'status_approved' => 'leave_approved',
            'status_assigned' => 'leave_assigned',
            'status_pending' => 'leave_requested',
        ];

        $statusName = $this->leave->status->name;

        if (!isset($events[$statusName])) {
            return $this;
        }

        $user = $leave->user_id;
        $event = $events[$statusName];

        $this->notify($events['status_assigned'], $user);

        if ($statusName === 'status_pending' && !$this->needToAddReview) {
            $user = $this->model->department->manager->id;
        }

        if ($this->needToAddReview) {
            $user = $this->department->parentDepartment->manager_id;
        }

        if ($statusName === 'status_pending') {
            $user = array_merge($this->getLeaveNotificationAudiences(), [$user]);
        }

        return $this->notify($event, $user);
    }

    public function getLeaveNotificationAudiences(): array
    {
        return User::query()
            ->where(function (Builder $builder) {
                $builder->whereHas('roles.permissions', function (Builder $builder) {
                    $builder->where('name', 'manage_approve_leave');
                })->whereHas('roles.permissions', function (Builder $builder) {
                    $builder->where('name', 'manage_reject_leave');
                })->whereHas('roles.permissions', function (Builder $builder) {
                    $builder->where('name', 'access_all_departments');
                });
            })->whereHas('roles', fn(Builder $builder) => $builder
                ->where('tenant_id', optional(tenant())->id))->pluck('id')->toArray();
    }

    public function notify($event, $users = []): self
    {
        notify()
            ->on($event)
            ->with($this->leave, $this->model)
            ->audiences($users)
            ->send(LeaveNotification::class);

        return $this;
    }
}
