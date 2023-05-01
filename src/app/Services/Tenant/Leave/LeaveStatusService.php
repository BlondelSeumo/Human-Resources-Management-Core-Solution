<?php

namespace App\Services\Tenant\Leave;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\HasWhen;
use App\Helpers\Traits\SettingKeyHelper;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Leave\LeaveStatus;
use App\Models\Tenant\Utility\Comment;
use App\Notifications\Tenant\LeaveNotification;
use App\Repositories\Core\Status\StatusRepository;
use App\Repositories\Tenant\Employee\DepartmentRepository;

class LeaveStatusService extends LeaveService
{
    use HasWhen, SettingKeyHelper;

    protected LeaveStatus $leaveStatus;

    protected string $leaveApprovalLevel;

    protected Department $department;

    protected string $statusName;

    protected bool $allowBypass;

    public bool $isNeedToUpdateLeave = false;

    public function setStatusName($status): self
    {
        $this->statusName = $status;
        return $this;
    }

    public function setSettings(): self
    {
        $this->leaveApprovalLevel = $this->getSettingFromKey('leave')('approval_level') ?: 'single';
        $this->allowBypass = $this->getSettingFromKey('leave')('allow_bypass') ?: false;

        return $this;
    }

    public function validationsAndSetCredentials(): self
    {
        $this->when($this->leaveApprovalLevel == 'multi',
            fn(LeaveStatusService $service) => $service->validationsAndSetMultiLevelCredentials(),
            fn(LeaveStatusService $service) => $service->validationsAndSetSingleLevelCredentials()
        );

        throw_if(
            (!$this->department->id && request()->get('access_behavior') == 'own_departments') &&
            !($this->leave->user_id == auth()->id() && $this->leave->status->name == 'status_pending' &&
            $this->statusName == 'canceled'),
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

    public function validationsAndSetMultiLevelCredentials(): self
    {
        $allowStatus = ['approved', 'rejected'];

        if ($this->allowBypass){
            $allowStatus = array_merge($allowStatus, ['bypassed']);
        }

        $conditions = $this->validationConditions($allowStatus);

        $conditionsTwo = $this->leave->lastStatus && ($this->leave->lastStatus->name == 'status_approved' ||
            $this->leave->lastStatus->name == 'status_bypassed');

        $conditionsThree = !$this->leave->lastStatus || $conditionsTwo;

        throw_if(
            !$conditions || !$conditionsThree,
            new GeneralException(__t('action_not_allowed'))
        );

        $conditionsForUpdateLeave = $this->department &&
            $this->department->parentDepartment &&
            $this->department->parentDepartment->id;

        $this->when(
            !$conditionsForUpdateLeave || ($this->statusName != 'approved' && $this->statusName != 'bypassed'),
            fn(LeaveStatusService $service) => $service->isNeedToUpdateLeave = true
        );

        return $this;
    }

    public function validationConditions($allowStatuses): bool
    {
        $conditionOne = $this->leave->user_id != auth()->id() || auth()->user()->isAppAdmin();

        $conditionTwo = $this->leave->status->name == 'status_approved' && $this->statusName == 'canceled';

        $conditionThree = $this->leave->status->name == 'status_pending' && (in_array($this->statusName, $allowStatuses));

        $conditionFour = $this->leave->user_id == auth()->id() && $this->leave->status->name == 'status_pending' &&
                $this->statusName == 'canceled';

        return ($conditionOne && ($conditionTwo || $conditionThree)) || $conditionFour;
    }

    public function validationsAndSetSingleLevelCredentials(): self
    {
        $allowStatus = ['approved', 'rejected'];

        $conditions = $this->validationConditions($allowStatus);

        throw_if(
            !$conditions,
            new GeneralException(__t('action_not_allowed'))
        );

        $this->isNeedToUpdateLeave = true;

        return $this;
    }

    public function setStatusAttr(): self
    {
        $status_name = "leave" . ucfirst($this->statusName);

        $status_id = resolve(StatusRepository::class)->$status_name();

        $this->setAttribute('status_id', $status_id);

        return $this;
    }

    public function updateLeaveStatus(): self
    {
        $this->leave->update([
            'status_id' => $this->getAttr('status_id')
        ]);

        return $this;
    }

    public function addLeaveReview(): self
    {
        $this->buildLeaveReview()
            ->endPreviousLastReview()
            ->createLeaveReview()
            ->createNote($this->leaveStatus, 'response-note');

        return $this;
    }

    public function endPreviousLastReview(): self
    {
        $this->leave->reviews()->update([
           'is_last' => 0
        ]);

        return $this;
    }

    public function createNote(LeaveStatus $leaveStatus, $type = 'reason-note'): self
    {
        $this->when($this->getAttr('note'), function () use ($leaveStatus, $type) {
            $leaveStatus->comments()->save(new Comment([
                'user_id' => $this->model->id,
                'type' => $type,
                'comment' => $this->getAttr('note')
            ]));
        });
        return $this;
    }

    public function createLeaveReview(): self
    {
        $this->leave->reviews()->save($this->leaveStatus);

        return $this;
    }

    public function buildLeaveReview(): self
    {
        if ($this->statusName == 'bypassed') {
            throw_if(
                !$this->department->parentDepartment,
                new GeneralException(__t('action_not_allowed'))
            );
        }

        $attributes = [
            'status_id' => $this->getAttr('status_id'),
            'reviewed_by' => auth()->id(),
            'department_id' => optional($this->department->parentDepartment)->id
        ];

        $this->leaveStatus = new LeaveStatus($attributes);
        return $this;
    }

    public function validateManger(): self
    {
        if (!$this->isCanceledByUser()){
            throw_if(
                !!$this->department->id && $this->department->manager->id != auth()->id(),
                new GeneralException(__t('action_not_allowed'))
            );
        }

        return $this;
    }

    public function isCanceledByUser(): bool
    {
        return $this->statusName == 'canceled' &&
            $this->leave->status->name == 'status_pending' && $this->model->id == auth()->id();
    }

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    public function setDepartment(): self
    {
        $this->department = new Department();

        $departmentId = $this->leave->lastStatus && $this->leave->lastStatus->pivot->department_id ?
            $this->leave->lastStatus->pivot->department_id :
            $this->model->department->id;

        $managerDept = resolve(DepartmentRepository::class)->getDepartments(auth()->id());
        $parentDept = resolve(DepartmentRepository::class)->getDepartments($departmentId, 'parents', 'department');

        foreach (array_reverse($parentDept) as $dept){
            if (in_array($dept, $managerDept)){
                $this->department = Department::find($dept)->load('manager', 'parentDepartment');
                break;
            }
        }

        return $this;
    }

    public function sendNotification(Leave $leave): self
    {
        $events = [
            'status_approved' => 'leave_approved',
            'status_rejected' => 'leave_rejected',
            'status_canceled' => 'leave_canceled',
            'status_bypassed' => 'leave_bypassed'
        ];

        $statusName = 'status_' . $this->statusName;

        if (!isset($events[$statusName])) {
            return $this;
        }

        $user = $leave->user_id;
        $event = $events[$statusName];

        if (!$this->isNeedToUpdateLeave) {
            $user = optional($this->department->parentDepartment)->manager_id;
            $event = $events['status_bypassed'];
        }

        return $this->notify($event, $user);
    }

    public function notify($event, $users = []): self
    {
        $model = $event == 'leave_bypassed' ? auth()->user() : $this->model;

        notify()
            ->on($event)
            ->with($this->leave, $model)
            ->audiences($users)
            ->send(LeaveNotification::class);

        return $this;
    }
}
