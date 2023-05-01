<?php

namespace App\Services\Tenant\WorkingShift;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\HasWhen;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\WorkingShift\DepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\UpcomingDepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\UpcomingUserWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use App\Notifications\Tenant\WorkingShiftNotification;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\TenantService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkingShiftService extends TenantService
{
    use HasWhen;

    protected array $workingShiftDetails = [];

    protected bool $isUpdating = false;

    protected array $users = [];

    protected int $workShiftId = 0;

    public function __construct(WorkingShift $working_shift)
    {
        $this->model = $working_shift;
    }

    public function update(): WorkingShiftService
    {
        $this->model->fill($this->getAttributes())->save();

        return $this;
    }

    public function saveDetails(): WorkingShiftService
    {
        $this->model->details()
            ->saveMany($this->hydrateDetailModels());

        return $this;
    }

    public function hydrateDetailModels(): array
    {
        return array_map(function ($data) {
            return WorkingShiftDetails::whereWeekday($data['weekday'])
                ->whereWorkingShiftId($this->model->id)
                ->firstOrNew()
                ->fill($data);
        }, $this->workingShiftDetails);
    }

    public function setWorkingShiftDetails($workingShiftDetails): WorkingShiftService
    {
        $this->workingShiftDetails = $workingShiftDetails;

        return $this;
    }

    public function checkIsDefault(): self
    {
        $isDefaultWorkShiftExists = WorkingShift::query()->where('is_default', 1)->exists();

        throw_if(
            $isDefaultWorkShiftExists && $this->getAttr('is_default') == 1,
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

    public function assignToUsers($users): WorkingShiftService
    {
        $users = is_array($users) ? $users : func_get_args();

        $users = array_merge($this->users, $users);

        $this->endPreviousWorkingShiftOfUsers($users);

        WorkingShiftUser::insert(
            array_map(
                fn($user) => array_merge([ 'user_id' => $user], self::getCommonPivotColumns()),
                WorkingShiftUser::getNoneExistedUserIds($this->getWorkShiftId(), $users)
            )
        );

        return $this;
    }

    public function assignToDepartments($departments = []): WorkingShiftService
    {
        $departments = is_array($departments) ? $departments : func_get_args();

        $this->endPreviousWorkingShiftsOfDepartments($departments);

        DepartmentWorkingShift::insert(
            array_map(
                fn ($department) => array_merge(['department_id' => $department], self::getCommonPivotColumns()),
                DepartmentWorkingShift::getNonExistDepartmentIds($this->getWorkShiftId(), $departments)
            )
        );

        $this->setUsers(
            resolve(DepartmentRepository::class)->employees($departments)->pluck('id')->toArray()
        );

        return $this;
    }

    public function endPreviousWorkingShiftsOfDepartments($departments = []): WorkingShiftService
    {
        if ($this->isUpdating && count($departments)){
            DepartmentWorkingShift::whereNull('end_date')
                ->where('working_shift_id',$this->getWorkShiftId())
                ->whereNotIn('department_id', $departments)
                ->update([
                    'end_date' => nowFromApp()
                ]);
        }

        DepartmentWorkingShift::whereNull('end_date')
            ->when(
                $this->isUpdating && !count($departments),
                fn (Builder $b) => $b->where('working_shift_id', $this->getWorkShiftId()),
                fn (Builder $b) => $b->where('working_shift_id', '!=', $this->getWorkShiftId())
                    ->whereIn('department_id', $departments)
            )->update([
               'end_date' => nowFromApp()
            ]);

        return $this;
    }

    public function endPreviousWorkingShiftOfUsers($users = []): WorkingShiftService
    {
        $removeUser = array_diff($this->model->users->pluck('id')->toArray(), $users);
        if(count($removeUser) && $this->isUpdating){
            WorkingShiftUser::whereNull('end_date')
                ->where('working_shift_id', $this->getWorkShiftId())
                ->whereIn('user_id', $removeUser)
                ->update([
                    'end_date' => nowFromApp()->format('Y-m-d')
                ]);
        }

        WorkingShiftUser::whereNull('end_date')
            ->when(
                $this->isUpdating && !count($users),
                fn (Builder $b) => $b->where('working_shift_id', $this->getWorkShiftId()),
                fn (Builder $b) => $b->where('working_shift_id', '!=', $this->getWorkShiftId())->whereIn('user_id', $users)
            )->update([
                'end_date' => nowFromApp()->format('Y-m-d')
            ]);

        return $this;
    }

    public function deleteDetails(): WorkingShiftService
    {
        WorkingShiftDetails::whereWorkingShiftId($this->model->id)->delete();

        return $this;
    }

    public function notify($event, $workingShit = null): WorkingShiftService
    {
        $workingShit = $workingShit ?: $this->model;

        notify()
            ->on($event)
            ->with($workingShit)
            ->send(WorkingShiftNotification::class);

        return $this;
    }

    public function setIsUpdating(bool $isUpdating): WorkingShiftService
    {
        $this->isUpdating = $isUpdating;
        return $this;
    }

    public function getCommonPivotColumns()
    {
        return [
            'working_shift_id' => $this->getWorkShiftId(),
            'start_date' => todayFromApp()->format('Y-m-d'),
            'end_date' => null
        ];
    }

    public function setUsers(array $users): WorkingShiftService
    {
        $this->users = $users;
        return $this;
    }

    public function setWorkShiftId(int $workShiftId): WorkingShiftService
    {
        $this->workShiftId = $workShiftId;

        return $this;
    }

    public function getWorkShiftId(): int
    {
        return $this->workShiftId ?: $this->model->id;
    }

    public function validateUsers()
    {
        validator($this->getAttributes(), [
            'users' => 'required|array'
        ]);

        return $this;
    }

    public function validateIfAttendanceNotExist($action)
    {
        throw_if(
            count($this->model->attendances),
            new GeneralException(__t('this_work_shift_already_have_attendance', ['action' => $action]))
        );

        return $this;
    }

    public function mergeNonAccessibleUsers($workingShift, $users = []): array
    {
        $DeptUsers = resolve(DepartmentRepository::class)->getDepartmentUsers(auth()->id());
        $workingShiftUsers = $this->model->load([
            'users' => fn($builder) => $builder
            ->whereDoesntHave('upcomingWorkingShift')
        ])->users->pluck('id')->toArray();

        $workingShiftUsers = array_merge(UpcomingUserWorkingShift::query()
            ->where('working_shift_id', $workingShift)
            ->pluck('user_id')
            ->toArray(), $workingShiftUsers);

        $cantUpdateUsers = array_diff($workingShiftUsers, $DeptUsers);

        $users = array_merge($cantUpdateUsers, $users);

        return $users;
    }

    public function assignToDepartmentAsUpcoming($departments = [], $workShiftId = null): self
    {
        $departments = is_array($departments) ? $departments : func_get_args();

        foreach ($departments as $department){
            UpcomingDepartmentWorkingShift::query()
                ->where('department_id', $department)
                ->delete();

            UpcomingUserWorkingShift::query()
                ->whereIn(
                    'user_id',
                    Department::query()
                        ->find($department)
                        ->load('users')
                        ->users
                        ->pluck('id')
                        ->toArray()
                )->delete();

            UpcomingDepartmentWorkingShift::query()->create([
                'working_shift_id' => $workShiftId ?: $this->getWorkShiftId(),
                'department_id' => $department,
                'start_date' => WorkingShift::query()->find($workShiftId ?: $this->getWorkShiftId())->start_date ?: todayFromApp()
            ]);
        }

        return $this;
    }

    public function assignToUserAsUpcoming($users = [], $workShiftId = null): self
    {
        $users = is_array($users) ? $users : func_get_args();
        $defaultWorkingShiftId = WorkingShift::getDefault()->id;
        $assigningWorkingShiftId = $workShiftId ?: $this->getWorkShiftId();

        foreach ($users as $user){
            $userWorkingShiftId = optional(User::query()->find($user)->load('workingShift')->workingShift)->id ?: $defaultWorkingShiftId;

            UpcomingUserWorkingShift::query()
                ->where('user_id', $user)
                ->delete();

            if ($userWorkingShiftId != $assigningWorkingShiftId){
                UpcomingUserWorkingShift::query()->create([
                    'working_shift_id' => $assigningWorkingShiftId,
                    'user_id' => $user,
                    'start_date' => WorkingShift::query()->find($assigningWorkingShiftId)->start_date ?: todayFromApp()
                ]);
            }
        }

        return $this;
    }

    public function assignUpdateToDepartmentAsUpcoming($departments = []): self
    {
        $departments = is_array($departments) ? $departments : func_get_args();

        $workShiftDepartments = $this->model->load([
            'departments' => fn(BelongsToMany $builder) => $builder->whereDoesntHave('upcomingWorkingShift')
        ])->departments->pluck('id')->toArray();

        $removeDepartments = $this->model->load([
            'departments' => fn(BelongsToMany $builder) => $builder->whereHas('upcomingWorkingShift')
        ])->departments->pluck('id')->toArray();

        $workShiftUpcomingDepartments = $this->model->load('upcomingDepartments')->upcomingDepartments->pluck('id')->toArray();

        $mergeDepartments = array_merge($workShiftDepartments,$workShiftUpcomingDepartments);
        $rollbackDepartments = array_intersect($departments, $removeDepartments);
        $requestDiff = array_diff($departments, array_merge($mergeDepartments, $rollbackDepartments));
        $databaseDiff = array_diff($workShiftDepartments, $departments);
        $upComingDiff = array_diff($workShiftUpcomingDepartments, $departments);
        $rollbackDepartments = array_merge($upComingDiff, $rollbackDepartments);

        if (!count($requestDiff) && !count($databaseDiff) && !count($rollbackDepartments)) {
            return $this;
        }

        if (count($rollbackDepartments)){
            foreach ($rollbackDepartments as $rollbackDept){
                UpcomingDepartmentWorkingShift::query()->where('department_id', $rollbackDept)->delete();
            }
        }

        if (count($requestDiff)) {
            $this->assignToDepartmentAsUpcoming($requestDiff);
        }

        if (count($databaseDiff)) {
            $this->assignToDepartmentAsUpcoming($databaseDiff, WorkingShift::getDefault()->id);
        }

        return $this;
    }

    public function assignUpdateToUserAsUpcoming($users = []): self
    {
        $users = is_array($users) ? $users : func_get_args();

        $workShiftUsers = $this->model->load([
            'users' => fn(BelongsToMany $builder) => $builder->whereDoesntHave('upcomingWorkingShift')
        ])->users->pluck('id')->toArray();

        $removeUsers = $this->model->load([
            'users' => fn(BelongsToMany $builder) => $builder->whereHas('upcomingWorkingShift')
        ])->users->pluck('id')->toArray();

        $workShiftUpcomingUsers = $this->model->load('upcomingUsers')->upcomingUsers->pluck('id')->toArray();

        $mergeUsers = array_merge($workShiftUsers, $workShiftUpcomingUsers);
        $rollbackUsers = array_intersect($users, $removeUsers);
        $requestDiff = array_diff($users, array_merge($mergeUsers, $rollbackUsers));
        $databaseDiff = array_diff($workShiftUsers, $users);
        $upComingDiff = array_diff($workShiftUpcomingUsers, $users);
        $rollbackUsers = array_merge($upComingDiff, $rollbackUsers);

        if (!count($requestDiff) && !count($databaseDiff) && !count($rollbackUsers)) {
            return $this;
        }

        if (count($rollbackUsers)){
            foreach ($rollbackUsers as $rollbackUser){
                UpcomingUserWorkingShift::query()->where('user_id', $rollbackUser)->delete();
            }
        }

        if (count($requestDiff)) {
            $this->assignToUserAsUpcoming($requestDiff);
        }

        if (count($databaseDiff)) {
            $this->assignToUserAsUpcoming($databaseDiff, WorkingShift::getDefault()->id);
        }

        return $this;
    }

    public function deleteUpcomingUsersAndDepartment(): self
    {
        UpcomingUserWorkingShift::query()->where('working_shift_id', $this->model->id)->delete();
        UpcomingDepartmentWorkingShift::query()->where('working_shift_id', $this->model->id)->delete();

        return $this;
    }

    public function departmentMoveChangeUpcomingWorkingShift($users = [])
    {
        $users = is_array($users) ? $users : func_get_args();

        $this->assignToUserAsUpcoming($users);
    }
}
