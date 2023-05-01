<?php

namespace App\Services\Tenant\Attendance;

use App\Exceptions\GeneralException;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Repositories\Core\Status\StatusRepository;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttendanceUpdateService extends AttendanceService
{
    public function duplicate(Attendance $attendance)
    {
        $this->when(
            $this->getAttr('move_to_log'),
            function (AttendanceUpdateService $service) {
                $statusApprove = resolve(StatusRepository::class)->attendanceApprove();
                $attributes = ['status_id' => $statusApprove];

                if (!$service->isNotFirstAttendance()) {
                    $attributes = array_merge([
                        'behavior' => $service->getUpdateBehavior($this->getAttr('in_time'))
                    ], $attributes);
                }

                $service->updateAttendance($attributes)
                    ->moveAttendanceDetailsToLog($this->details);
            }
        );

        /** @var AttendanceDetails $newDetails */
        $newDetails = $attendance->details()->save(new AttendanceDetails([
            'in_time' => $this->getAttr('in_time') ? $this->carbon($this->getAttr('in_time'))->toDateTime() : null,
            'out_time' => $this->getAttr('out_time') ? $this->carbon($this->getAttr('out_time'))->toDateTime() : null,
            'added_by' => auth()->id(),
            'review_by' => $this->getAttr('move_to_log') ? auth()->id() : null,
            'attendance_details_id' => $this->details->id,
            'status_id' => $this->getAttr('status_id'),
            'in_ip_data' => $this->getAttr('in_ip_data'),
            'out_ip_data' => $this->getAttr('out_ip_data')
        ]));

        return $this->createNote($newDetails, $this->getAttr('move_to_log') ? 'manual' : 'request')
            ->sendNotification($newDetails->load('status'));
    }

    public function validateForRequest(): AttendanceUpdateService
    {
        validator($this->getAttrs('in_time', 'out_time', 'note'), [
            'note' => 'required|min:10',
            'in_time' => [Rule::requiredIf(!$this->getAttr('out_time')), 'different:' . $this->carbon($this->details->in_time)->toDateTime()],
            'out_time' => [Rule::requiredIf(!$this->getAttr('in_time')), 'different:' . $this->carbon($this->details->out_time)->toDateTime()],
        ])->validate();

        throw_if(
            ($this->getAttr('out_time') && !$this->details->out_time),
            ValidationException::withMessages([
                'out_time' => [__t('out_time_existing_warning')]
            ])
        );

        return $this;
    }

    public function validateAttendanceRequestDate(): AttendanceUpdateService
    {
        if ($this->getAttr('in_time')) {

            $this->validatePunchInDate($this->getAttr('in_time'), $this->details->attendance->in_date);

            if ($this->getAttr('out_time')) {
                throw_if(
                    $this->carbon($this->getAttr('in_time'))->parse()->diffInHours($this->carbon($this->getAttr('out_time'))->parse()) >= 24,
                    ValidationException::withMessages([
                        'out_time' => [__t('punch_in_and_out_time_difference_message')]
                    ])
                );
            }

        }
        return $this;
    }

    public function validateOwner()
    {
        if (auth()->user()->can('update_attendances')) {
            return $this;
        }

        throw_if(
            $this->model->id != $this->details->attendance->user_id,
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

    public function validateWorkShift()
    {
        $workShift = $this->workShift($this->details->attendance->workingShift);

        throw_if(
            $this->carbon($this->getToday())->toDayInLowerCase() != $workShift->weekday,
            new GeneralException(__t('conflict_with_previous_working_shift_time'))
        );

        return $this;
    }

    public function validateAlreadyRequested()
    {
        $attendanceApprovePending = resolve(StatusRepository::class)->attendanceApprovePending();

        $isExist = AttendanceDetails::whereAttendanceDetailsId($this->details->id)
            ->whereIn('status_id', $attendanceApprovePending)
            ->exists();

        throw_if(
            $isExist,
            new GeneralException(__t('already_requested_for_change'))
        );

        return $this;
    }

    public function validateIfApproved()
    {
        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        throw_if(
            $this->details->status_id != $attendanceApprove,
            new GeneralException(__t('not_approve_for_change_warning'))
        );

        return $this;
    }

    public function getStatusAttribute()
    {
        if ($this->autoApproval()) {
            return [
                'status_id' => resolve(StatusRepository::class)->attendanceApprove(),
                'move_to_log' => true
            ];
        }

        return [
            'status_id' => resolve(StatusRepository::class)->attendancePending(),
            'move_to_log' => false
        ];
    }

}
