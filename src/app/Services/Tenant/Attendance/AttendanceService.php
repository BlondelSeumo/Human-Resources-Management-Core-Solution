<?php

namespace App\Services\Tenant\Attendance;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\HasWhen;
use App\Helpers\Core\Traits\Memoization;
use App\Helpers\Traits\AutoApprovalTrait;
use App\Helpers\Traits\DateTimeHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Utility\Comment;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use App\Notifications\Tenant\AttendanceNotification;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AttendanceService extends TenantService
{
    use Memoization, HasWhen, SettingKeyHelper, DateTimeHelper, AutoApprovalTrait;

    protected AttendanceDetails $details;

    protected $workShiftDetails;

    public function punchIn()
    {
        $statusApproved = resolve(StatusRepository::class)->attendanceApprove();

        $this->validatePunchIn()
            ->saveDetails()
            ->when(
                $this->details->attendance->status_id != $statusApproved,
                function (AttendanceService $service) use ($statusApproved) {
                    $service->updateAttendanceStatus($statusApproved);
                }
            );
    }

    public function updateAttendanceStatus($status_id)
    {
        $this->details->attendance()->update([
            'status_id' => $status_id
        ]);
    }

    public function saveDetails(): AttendanceService
    {
        $this->when($this->getAttr('punch_in'), function (AttendanceService $service) {

            $service->buildInOutDetails([
                'in_ip_data' => count($this->getAttr('ip_data', [])) ? json_encode($this->getAttr('ip_data')) : null,
            ])
                ->createDetails($this->todayAttendance())
                ->createNote($this->details);

        }, function (AttendanceService $service) {
            $details = AttendanceDetails::getUnPunchedOut($service->model->id);

            $details->update([
                'out_time' => $this->getNow(),
                'out_ip_data' => count($this->getAttr('ip_data', [])) ? json_encode($this->getAttr('ip_data')) : null,
            ]);

            $service->createNote($details, 'out-note');

        });
        return $this;
    }

    public function createNote(AttendanceDetails $details, $type = 'in-note'): AttendanceService
    {
        $this->when($this->getAttr('note'), function () use ($details, $type) {
            $details->comments()->save(new Comment([
                'user_id' => $this->hasAttribute('note_user_id') ? $this->getAttr('note_user_id') : auth()->id(),
                'type' => $type,
                'comment' => $this->getAttr('note')
            ]));
        });

        return $this;
    }

    public function createDetails(Attendance $attendance): AttendanceService
    {
        $attendance->details()->save($this->details);

        return $this;
    }

    public function buildInOutDetails(array $attributes = []): AttendanceService
    {
        $attributes = array_merge($attributes, [
            'in_time' => $this->getNow(),
            'status_id' => $this->getAttr('status_id')
        ]);

        $this->details = new AttendanceDetails($attributes);

        return $this;
    }

    public function getNow(): Carbon
    {
        return $this->getAttr('now') ? $this->carbon($this->getAttr('now'))->parse() : nowFromApp();
    }

    public function todayAttendance($type = null): Attendance
    {
        return $this->memoize('today-attendance', function () use ($type) {
            return $this->model
                ->attendances()
                ->whereDate('in_date', $this->getPunchInDate($type))
                ->firstOr(fn() => $this->createAttendance($type));
        }, $this->refreshMemoization);
    }

    public function getPunchInDate($type = null)
    {
        $workShift = $this->workShift($this->getWorkingShift($type));

        if (!$workShift) {
            return $this->getNow();
        }

        return $this->carbon($this->getToday())->toDayInLowerCase() == $workShift->weekday ? $this->getToday() : $this->getToday()->subDay();
    }

    public function workShift(WorkingShift $workingShift)
    {
        return $this->workShiftDetails = WorkingShiftDetails::whereWorkingShiftId($workingShift->id)
            ->where(function (Builder $builder) {
                $builder->where(function (Builder $builder) {
                    $builder->whereRaw(
                        DB::raw("IF(TIME(start_at) > TIME(end_at), IF(TIME(end_at) >= ?, 1, 0), 0)=1"),
                        [$this->carbon($this->getNow())->toTime()]
                    )->where('weekday', $this->carbon($this->getToday()->subDay())->toDayInLowerCase());
                })->orWhere('weekday', $this->carbon($this->getToday())->toDayInLowerCase());
            })->first();
    }

    public function getWorkingShift($type = null)
    {
        return $this->memoize('working-shift-' . $this->model->id, function () use ($type) {
            if ($type == 'manual') {
                $userWorkShift = WorkingShiftUser::query()
                    ->where('user_id', $this->model->id)
                    ->whereDate('start_date', '<=', $this->getToday())
                    ->whereDate('end_date', '>', $this->getToday())
                    ->first();

                $workShift = $userWorkShift ? WorkingShift::query()->find($userWorkShift->working_shift_id) :
                    optional($this->model->load('workingShift:id'))->workingShift;

            } else {
                $workShift = optional($this->model->load('workingShift:id'))->workingShift;
            }

            if (!optional($workShift)->id) {
                $workShift = WorkingShift::getDefault(['id']);
            }

            return $workShift;
        }, $this->refreshMemoization);
    }

    public function getToday(): Carbon
    {
        return $this->getAttr('today') ? $this->carbon($this->getAttr('today'))->parse() : todayFromApp();
    }

    public function createAttendance($type = null)
    {
        return $this->model->attendances()
            ->save(new Attendance([
                'in_date' => $this->getPunchInDate(),
                'status_id' => $this->getAttr('status_id'),
                'working_shift_id' => $this->getWorkingShift($type)->id,
                'behavior' => $this->getBehavior($this->workShiftDetails)
            ]));
    }

    public function getBehavior(WorkingShiftDetails $details): string
    {
        $setting = $this->getSettingFromKey('attendance')('punch_in_time_tolerance');

        $setting = (int)$setting;

        if (!$details->start_at) {
            return 'regular';
        }

        $workShiftTime = $this->carbon($this->getToday()->toDateString() . ' ' . $details->start_at, 'UTC')->parse();

        if ($this->getNow()->isBefore($workShiftTime)) {
            return 'early';
        }

        if ($this->getNow()->isAfter($workShiftTime->addMinutes($setting))) {
            return 'late';
        }

        return 'regular';

    }

    public function validatePunchIn(): AttendanceService
    {
        $is_not_punched_out = $this->checkPunchIn();

        throw_if(
            $is_not_punched_out,
            ValidationException::withMessages(['out_time' => [__t('you_must_punch_out_message')]])
        );

        return $this;
    }

    public function checkPunchIn(): bool
    {
        $details = AttendanceDetails::getUnPunchedOut($this->model->id);

        return optional($details)->id ? true : false;
    }

    public function getPunchInOutAlert()
    {
        $user = auth()->user()->load([
            'workingShift',
            'workingShift.details',
            'department',
            'department.workingShift',
            'department.holidays'
        ]);
        $workShift = $user->workingShift ?? $user->department->workingShift;
        if (!$workShift) {
            $workShift = WorkingShift::where('is_default', 1)->with('details')->first();
        }
        $now = now(request('timeZone'));
        $detail = $workShift->details->first(
            fn($value, $key) => $value->weekday === strtolower($now->format('D'))
        );
        if ($detail->is_weekend) {
            return ['showAlert' => false];
        }
        $shiftStart = $this->carbon($detail->start_at)->parse()->setTimezone(request('timeZone'));
        $shiftEnd = $this->carbon($detail->end_at)->parse()->setTimezone(request('timeZone'));
        if ($this->setModel($user)->checkPunchIn()) {
            if ($now > $shiftEnd) {
                return [
                    'showAlert' => true,
                    'message' => __t('you_have_not_punched_out')
                ];
            }
        } else {
            if ($now >= $shiftStart && $now <= $shiftEnd) {
                return [
                    'showAlert' => true,
                    'message' => __t('you_have_not_punched_in')
                ];
            }
        }
        return ['showAlert' => false];
    }

    public function punchOut()
    {
        $this->validatePunchOut()
            ->saveDetails();
    }

    public
    function validatePunchOut(): AttendanceService
    {
        $is_punched_out = $this->checkPunchIn();

        throw_if(
            !$is_punched_out,
            ValidationException::withMessages(['in_time' => [__t('you_dont_punch_in_yet')]])
        );

        return $this;
    }

    public
    function manualAddPunch()
    {
        $this->checkPreviousPunchIn()
            ->validateExistingPunchTime();

        $this->setAttr('today', $this->getAttr('in_date'));
        $this->setAttr('now', $this->getAttr('in_time'));

        $attributes = [
            'out_time' => $this->carbon($this->getAttr('out_time'))->toDateTime(),
            'review_by' => $this->getAttr('review_by'),
            'added_by' => $this->getAttr('added_by')
        ];

        $this->buildInOutDetails($attributes)
            ->saveManualDetails($this->todayAttendance('manual'))
            ->createNote(
                $this->details,
                $this->getAttr('note_type')
            )->sendNotification($this->details->load('status'));

        return $this;
    }

    public
    function manualAddPunchForSeeder()
    {
        $this->checkPreviousPunchIn()
            ->validateExistingPunchTime();

        $this->setAttr('today', $this->getAttr('in_date'));
        $this->setAttr('now', $this->getAttr('in_time'));

        $attributes = [
            'out_time' => $this->carbon($this->getAttr('out_time'))->toDateTime(),
            'review_by' => $this->getAttr('review_by'),
            'added_by' => $this->getAttr('added_by')
        ];

        $this->buildInOutDetails($attributes)
            ->saveManualDetails($this->todayAttendance('manual'))
            ->createNote(
                $this->details,
                $this->getAttr('note_type')
            );

        return $this;
    }

    public
    function validateExistingPunchTime($changeDetailsId = null)
    {
        $inTime = $this->carbon($this->getAttr('in_time'))->toDateTime();
        $outTime = $this->carbon($this->getAttr('out_time'))->toDateTime();

        $statuses = resolve(StatusRepository::class)->attendanceApprovePending();

        $attendanceDetails = AttendanceDetails::query()
            ->whereHas('attendance', fn(Builder $builder) => $builder->where('user_id', $this->model->id))
            ->when(!$outTime, fn(Builder $b) => $b
                ->where('in_time', '>=', $inTime)
                ->where('out_time', '<=', $inTime),
                fn(Builder $builder) => $builder
                    ->where(function (Builder $builder) use ($inTime, $outTime) {
                        $builder
                            ->whereBetween('in_time', [$inTime, $outTime])
                            ->orWhereBetween('out_time', [$inTime, $outTime])
                            ->orWhere(function ($query) use ($inTime, $outTime) {
                                $query->where('in_time', '<=', $inTime)
                                    ->where('out_time', '>=', $outTime);
                            });
                    })
            )->whereIn('status_id', $statuses)
            ->when($changeDetailsId,
                fn(Builder $builder) => $builder->where('id', '!=', $this->details->id)
            )->exists();

        throw_if(
            $attendanceDetails,
            new GeneralException(__t('conflict_with_previous_attendances'))
        );

        return $this;
    }

    public
    function validateIfAlreadyRequested($attendanceDetailsId): self
    {
        $statusPending = resolve(StatusRepository::class)->attendancePending();

        $isExists = AttendanceDetails::query()
            ->where('attendance_details_id', $attendanceDetailsId)
            ->where('status_id', $statusPending)
            ->exists();

        throw_if(
            $isExists,
            new GeneralException(__t('already_requested_for_update_this_attendance'))
        );

        return $this;
    }

    public
    function checkPreviousPunchIn(): AttendanceService
    {
        $is_previous_punch_in = $this->model
            ->attendances()
            ->whereHas('details', function (Builder $builder) {
                $builder
                    ->whereNull('out_time')
                    ->where('in_time', '<=', $this->carbon($this->getAttr('out_time'))->toDateTime());
            })
            ->exists();

        throw_if(
            $is_previous_punch_in,
            ValidationException::withMessages(['out_time' => [__t('employee_is_punched_in_message')]])
        );

        return $this;
    }

    public
    function saveManualDetails(Attendance $attendance)
    {
        $attendance
            ->details()
            ->save($this->details);

        return $this;
    }

    public
    function validateManual(): AttendanceService
    {
        validator($this->getAttributes(), [
            'employee_id' => 'required',
            'in_date' => 'required',
            'in_time' => 'required',
            'out_time' => 'required'
        ])->validate();

        $inTime = $this->carbon($this->getAttr('in_time'))->parse();
        $outTime = $this->carbon($this->getAttr('out_time'))->parse();

        throw_if(
            $inTime->diffInHours($outTime) >= 24,
            ValidationException::withMessages(['out_time' => [__t('punch_in_and_out_time_difference_message')]])
        );

        return $this;
    }

    public
    function validateIfNotFuture()
    {
        $inTimeRules = $this->getAttr('out_time') ? 'nullable|before:out_time|before_or_equal:' . nowFromApp() : 'nullable|before_or_equal:' . nowFromApp();
        $outTimeRules = $this->getAttr('in_time') ? 'nullable|after:in_time|before_or_equal:' . nowFromApp() : 'nullable|before_or_equal:' . nowFromApp();

        validator($this->getAttributes(), [
            'in_time' => $inTimeRules,
            'out_time' => $outTimeRules
        ], [
            'in_time.before_or_equal' => 'The in time must be a date before or equal now',
            'out_time.before_or_equal' => 'The out time must be a date before or equal now',
        ])->validate();

        return $this;
    }

    public
    function validatePunchInDate($date, $compareTo = null): AttendanceService
    {
        if (!$date) {
            throw new GeneralException(__t('action_not_allowed'));
        }

        throw_if(
            $this->carbon($this->carbon($date)->parse()->toDateString())->parse()
                ->diffInDays($compareTo ? $compareTo : todayFromApp()) > 1,
            ValidationException::withMessages([
                'in_time' => [__t('attendance_can_not_be_applied_in_different_day')]
            ])
        );

        return $this;
    }

    public
    function validateOwnDepartmentUser(): AttendanceService
    {
        if (auth()->user()->isAppAdmin()) return $this;

        throw_if(
            $this->model->department->manager->id != auth()->id(),
            new GeneralException(__t('this_employee_does_not_belongs_to_your_department'))
        );

        return $this;
    }

    public
    function setDetails(AttendanceDetails $details): AttendanceService
    {
        $this->details = $details;
        return $this;
    }

    public
    function attendanceFromDate($date): Attendance
    {
        return $this->memoize(
            'attendance-' . $date,
            fn() => Attendance::date($this->carbon($date)->parse())
                ->firstOrFail(),
            $this->refreshMemoization
        );
    }

    public
    function moveAttendanceDetailsToLog(AttendanceDetails $details)
    {
        $attendanceLog = resolve(StatusRepository::class)->attendanceLog();

        $details->fill([
            'status_id' => $attendanceLog
        ])->save();

        return $this;
    }

    public
    function updateAttendance($attributes = []): AttendanceService
    {
        $this->when(count($attributes),
            fn(AttendanceService $service) => $this->details->attendance()->update($attributes)
        );

        return $this;
    }

    public
    function isNotFirstAttendance()
    {
        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        return AttendanceDetails::where('attendance_id', $this->details->attendance_id)
            ->where(fn(Builder $builder) => $builder
                ->where('id', $this->details->id)
                ->orWhere('attendance_details_id', $this->details->attendance_details_id)
            )->where('in_time', '>', $this->details->in_time)
            ->where('status_id', $attendanceApprove)
            ->exists();
    }

    public
    function getUpdateBehavior($inTime = null): string
    {
        $workShift = WorkingShift::find($this->details->attendance->working_shift_id);

        if ($inTime) {
            $this->setAttr('now', $inTime);
        } else {
            $this->setAttr('now', $this->details->in_time);
        }
        $this->setAttr('today', $this->details->attendance->in_date);

        $workShiftDetails = $this->workShift($workShift);

        return $this->getBehavior($workShiftDetails);
    }

    public
    function getUpdateAttendanceStatus()
    {
        if ($this->isStatusExists('approve')) {
            $status_name = 'approve';
        } elseif ($this->isStatusExists('pending')) {
            $status_name = 'pending';
        } else {
            $status_name = Str::replaceFirst('status_', '', $this->details->load('status')->status->name);
        }

        $method = 'attendance' . ucfirst($status_name);

        return resolve(StatusRepository::class)->$method();
    }

    public
    function isStatusExists($name): bool
    {
        $method = 'attendance' . ucfirst($name);

        $status = resolve(StatusRepository::class)->$method();

        return AttendanceDetails::where('attendance_id', $this->details->attendance_id)
            ->where('status_id', $status)
            ->exists();
    }

    public
    function autoApproval(): bool
    {
        $settings = (object)$this->getSettingFromKeys('attendance')('auto_approval', 'users', 'roles');

        if ($this->autoSettingsApproval($settings)) {
            return true;
        }

        if ((auth()->user()->can('update_attendances') &&
                ($this->model->id != auth()->id() || auth()->user()->isAppAdmin()))
            && request()->get('access_behavior') != 'own_departments') {
            return true;
        }

        if (auth()->user()->can('update_attendances') &&
            request()->get('access_behavior') == 'own_departments' && $this->model->id != auth()->id()) {

            return true;
        }
        return false;
    }

    public
    function notify($event, $details, $users = []): self
    {
        notify()
            ->on($event)
            ->with($details, $this->model)
            ->audiences($users)
            ->send(AttendanceNotification::class);

        return $this;
    }

    public
    function sendNotification(AttendanceDetails $details): self
    {
        $events = [
            'status_pending' => 'attendance_requested',
            'status_approve' => 'attendance_approved',
            'status_reject' => 'attendance_rejected',
            'status_cancel' => 'attendance_canceled'
        ];

        if (!isset($events[optional($details->status)->name])) {
            return $this;
        }

        $users = $details->attendance->user_id;
        $event = $events[optional($details->status)->name];

        if ($event === 'attendance_requested') {
            $users = User::query()
                ->where(function (Builder $builder) {
                    $builder->whereHas('roles.permissions', function (Builder $builder) {
                        $builder->where('name', 'approve_attendance');
                    })->whereHas('roles.permissions', function (Builder $builder) {
                        $builder->where('name', 'reject_attendance');
                    })->whereHas('roles.permissions', function (Builder $builder) {
                        $builder->where('name', 'access_all_departments');
                    });
                })->whereHas('roles', fn(Builder $builder) => $builder
                    ->where('tenant_id', optional(tenant())->id))->pluck('id')->toArray();

            $manager = $details->attendance->user->department->first()->manager;
            $canApproveAndReject = $manager->whereHas('roles.permissions', function (Builder $builder) {
                $builder->where('name', 'approve_attendance');
            })->whereHas('roles.permissions', function (Builder $builder) {
                $builder->where('name', 'reject_attendance');
            })->exists();

            if ($canApproveAndReject) {
                in_array($manager->id, $users) ?: $users = array_merge($users, [$manager->id]);
            }
        }
        return $this->notify($event, $details, $users);
    }

}