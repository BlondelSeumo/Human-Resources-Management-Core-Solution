<?php

namespace App\Services\Tenant\Attendance;

use App\Exceptions\GeneralException;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Core\Status\StatusRepository;

class AttendanceStatusService extends AttendanceService
{
    public function updateAttendanceDetailsStatus(): self
    {
        $this->validateRequest()
            ->validateUser()
            ->updateStatus()
            ->updateParentAttendance()
            ->logParentAttendanceDetails();

        return $this->sendNotification($this->details->load('status'));
    }

    public function validStatus($statusName): self
    {
        throw_if(
            !in_array($statusName, Attendance::$statuses),
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

    public function validateUser(): self
    {
        $permissions = (auth()->user()->can('update_attendance_status') &&
            ($this->details->attendance->user_id != auth()->id() || auth()->user()->isAppAdmin())) ||
            (
                $this->getAttr('requestedStatus') == 'cancel' &&
                $this->getAttr('previousStatus') == 'status_pending' &&
                $this->details->attendance->user_id == auth()->id()
            );

        throw_if(
            !$permissions,
            new GeneralException(__t('action_not_allowed'))
        );
        
        return $this;
    }

    public function logParentAttendanceDetails(): self
    {
        $this->when(
            ($this->isChangeRequest() && $this->getAttr('requestedStatus') == 'approve'),
            fn(AttendanceStatusService $service) => $service->moveAttendanceDetailsToLog($this->details->parentAttendanceDetails)
        );

        return $this;
    }

    public function updateParentAttendance(): self
    {
        $attributes = ['status_id' => $this->getUpdateAttendanceStatus()];

        if ($this->getAttr('requestedStatus') == 'approve' && !$this->isNotFirstAttendance()) {
            $attributes = array_merge([
                'behavior' => $this->getUpdateBehavior()
            ], $attributes);
        }

        $this->details->attendance()->update($attributes);

        return $this;
    }

    public function updateStatus(): AttendanceStatusService
    {
        $attributes = [
            'status_id' => $this->getAttr('status_id'),
            'review_by' => $this->getAttr('review_by')
        ];

        if ($this->getAttr('requestedStatus') == 'approve' &&
            $this->isChangeRequest() && !$this->details->out_time) {
            $attributes = array_merge([
                'out_time' => $this->details->parentAttendanceDetails->out_time
            ], $attributes);
        }

        $this->details->update($attributes);

        return $this;
    }

    public function isChangeRequest(): bool
    {
        return $this->details->attendance_details_id ? true : false;
    }

    public function validateRequest(): AttendanceStatusService
    {
        throw_unless(
            ($this->getAttr('previousStatus') == 'status_pending' && (
                $this->getAttr('requestedStatus') == 'approve' ||
                $this->getAttr('requestedStatus') == 'reject'
            )) ||
            ($this->getAttr('previousStatus') == 'status_pending' &&
                $this->getAttr('requestedStatus') == 'cancel' &&
                $this->model->id == auth()->id()) ||
            (!!$this->details->added_by &&
                $this->getAttr('previousStatus') == 'status_approve' &&
                $this->getAttr('requestedStatus') == 'cancel'),

            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }
}
