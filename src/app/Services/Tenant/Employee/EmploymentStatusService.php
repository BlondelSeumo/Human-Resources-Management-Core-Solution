<?php


namespace App\Services\Tenant\Employee;


use App\Exceptions\GeneralException;
use App\Models\Core\Auth\Traits\Method\UserStatus;
use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\Employee\UserEmploymentStatus;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\TenantService;

class EmploymentStatusService extends TenantService
{
    use UserStatus;
    protected int $employmentStatusId = 0;

    public function __construct(EmploymentStatus $employmentStatus)
    {
        $this->model = $employmentStatus;
    }

    public function assignToUsers($users)
    {
        $users = is_array($users) ? $users : func_get_args();

        UserEmploymentStatus::insert(
            array_map(fn($user) => [
                'user_id' => $user,
                'start_date' => $this->getAttribute('start_date') ?: todayFromApp()->format('Y-m-d'),
                'employment_status_id' => $this->getEmploymentStatusId(),
                'description' => $this->getAttribute('description')
            ], UserEmploymentStatus::getNoneExistedUsers($this->getEmploymentStatusId(), $users))
        );

        if (EmploymentStatus::query()->find($this->getEmploymentStatusId())->alias == 'terminated') {
            User::query()->whereIn('id', $users)->update([
                'status_id' => resolve(StatusRepository::class)->userInactive()
            ]);
        }

        return $this;
    }

    public function endPreviousEmploymentStatus($users = [])
    {
        $users = is_array($users) ? $users : func_get_args();

        UserEmploymentStatus::query()
            ->whereNull('end_date')
            ->where('employment_status_id', '!=', $this->getEmploymentStatusId())
            ->whereIn('user_id', $users)
            ->update([
                'end_date' => $this->getAttribute('end_date') ?: todayFromApp()->format('Y-m-d'),
                'description' => $this->getAttribute('description')
            ]);

        return $this;
    }

    public function setEmploymentStatusId(int $employmentStatusId): EmploymentStatusService
    {
        $this->employmentStatusId = $employmentStatusId;
        return $this;
    }


    public function getEmploymentStatusId(): int
    {
        return $this->employmentStatusId ?: $this->model->id;
    }

    public function changeEmployeeStatus($users, $status): EmploymentStatusService
    {
        throw_if(
            is_array($status),
            new GeneralException('Status can\'t be an array')
        );
        if ($status instanceof Status) {
            $status = $status->id;
        }elseif (is_string($status)) {
            $methodName = 'user'.ucfirst($status);
            $status = resolve(StatusRepository::class)->$methodName();
        }

        $users = is_array($users) ? $users : func_get_args();
        User::query()
            ->whereIn('id', $users)
            ->update(['status_id' => $status]);

        return $this;
    }

    public function update()
    {
        $this->model->update($this->getAttrs());
    }

    public function validations(): self
    {
        validator($this->getAttrs(), [
            'name' => '|unique:employment_statuses,name,'.$this->model->id,
            'class' => 'required|in:purple,success,info,warning,primary,danger'
        ])->validate();

        return $this;
    }

}
