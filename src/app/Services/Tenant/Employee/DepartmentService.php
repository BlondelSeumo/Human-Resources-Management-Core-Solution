<?php


namespace App\Services\Tenant\Employee;


use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\HasWhen;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\WorkingShift\DepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\UpcomingDepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\UpcomingUserWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;
use App\Notifications\Tenant\DepartmentNotification;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\TenantService;
use Illuminate\Validation\ValidationException;

class DepartmentService extends TenantService
{
    use HasWhen;

    protected Role $role;
    protected User $current_manager;

    public bool $isUpdate = false;

    public function __construct(Department $department, Role $role)
    {
        $this->model = $department;
        $this->role = $role;
    }

    public function setCurrentManager($manager_id): DepartmentService
    {
        $this->current_manager = User::findOrFail($manager_id);
        return $this;
    }

    public function validateParent()
    {
        $ignore = optional($this->model)->id ?: 0;
        $model = $this->model->whereNull('department_id')->first();

        if ($ignore && $ignore == optional($model)->id) {
            return $this;
        }

        $condition = optional($model)->id &&
            !$this->getAttribute('department_id');

        throw_if(
            $condition,
            ValidationException::withMessages([
                'department_id' => __t('parent_already_exists_warning')
            ])
        );

        return $this;
    }

    public function validateForParentDepartments(): self
    {
        if ($this->getAttr('department_id') && $this->model->id){
            $childDepartments = resolve(DepartmentRepository::class)
                ->getDepartments($this->model->id, 'children', 'department');

            throw_if(
                in_array($this->getAttr('department_id'), $childDepartments),
                ValidationException::withMessages([
                    'department_id' => __t('cant_add_child_department_as_parent_department')
                ])
            );
        }

        return $this;

    }

    public function departmentManagerRole()
    {
        $role = $this->role
            ->where('is_default', 1)
            ->where('is_admin', 0)
            ->where('alias', 'department_manager')
            ->where('tenant_id', tenant()->id)
            ->first();

        if ($role) {
            return $role;
        }

        throw new GeneralException(__t('manager_role_not_found'));
    }

    public function checkAndResetDepartmentManager(): DepartmentService
    {
        if($this->current_manager->hasDepartments->count() > 1){
            return $this;
        }
        $this->current_manager->roles()->detach($this->departmentManagerRole());

        return $this;
    }

    public function update()
    {
        $this->model->fill($this->getAttributes());

        $this->when($this->model->isDirty(), function (DepartmentService $service) {
            $service->notify('department_updated');
        });

        $this->when($this->model->isDirty('manager_id'), function (DepartmentService $service) {
            $service->checkAndResetDepartmentManager();
            $service->makeUserDepartmentManager();
        });

        $this->model->save();

        return $this;
    }

    public function assignWorkingShift(): self
    {
        $department = $this->model->load('workingShift');
        $departmentWorkShift = $department->workingShift;
        $defaultWorkingShift = WorkingShift::getDefault(['id']);

        if (!$departmentWorkShift) {
            $departmentWorkShift = $defaultWorkingShift;
        }

        if ($departmentWorkShift->id != $this->getAttr('working_shift_id')) {
            $this->endPreviousWorkingShift();

            $this->when($defaultWorkingShift->id != $this->getAttr('working_shift_id'),
                function (DepartmentService $service) {
                    DepartmentWorkingShift::insert([
                        'department_id' => $service->model->id,
                        'working_shift_id' => $service->getAttr('working_shift_id'),
                        'start_date' => todayFromApp()->format('Y-m-d'),
                        'end_date' => null
                    ]);
                }
            );
            if ($this->isUpdate){
                $this->assignDepartmentUsersToWorkShift(
                    $department->users()->pluck('id')->toArray()
                );
            }
        }

        return $this;
    }

    public function setIsUpdate(bool $value): self
    {
        $this->isUpdate = $value;

        return $this;
    }

    public function assignDepartmentUsersToWorkShift($users)
    {
        $users = is_array($users) ? $users : func_get_args();

        $this->endPreviousUserWorkingShift($users);

        $defaultWorkingShift = WorkingShift::getDefault(['id']);

        if ($defaultWorkingShift->id != $this->getAttr('working_shift_id')){
            WorkingShiftUser::insert(
                array_map(
                    fn($user) => array_merge([ 'user_id' => $user], [
                        'working_shift_id' => $this->getAttr('working_shift_id'),
                        'start_date' => todayFromApp()->format('Y-m-d'),
                        'end_date' => null
                    ]),
                    WorkingShiftUser::getNoneExistedUserIds($this->getAttr('working_shift_id'), $users)
                )
            );
        }
    }

    public function endPreviousUserWorkingShift($users): self
    {
        WorkingShiftUser::query()
            ->whereIn('user_id', $users)
            ->whereNull('end_date')
            ->update([
               'end_date' => nowFromApp()->format('Y-m-d')
            ]);

        return $this;
    }

    public function endPreviousWorkingShift(): self
    {
        DepartmentWorkingShift::whereNull('end_date')
            ->where('department_id', $this->model->id)
            ->update([
                'end_date' => nowFromApp()
            ]);

        return $this;
    }

    public function makeUserDepartmentManager(): DepartmentService
    {
        $this->when($this->getAttribute('manager_id'), function () {
            $this->model->manager->assignRole($this->departmentManagerRole());
        });

        return $this;
    }

    public function notify($event, $department = null)
    {
        $model = $department ?: $this->model;

        notify()
            ->on($event)
            ->with($model)
            ->send(DepartmentNotification::class);

        return $this;
    }

    public function deleteAssignWorkShift($department): self
    {
        $department->workingshifts()->delete();

        return $this;
    }

    public function assignUpcomingWorkShift(): self
    {
        $department = $this->model->load('workingShift');
        $departmentWorkShift = $department->workingShift;
        $defaultWorkingShift = WorkingShift::getDefault(['id']);

        if (!$departmentWorkShift) {
            $departmentWorkShift = $defaultWorkingShift;
        }
        if ($departmentWorkShift->id != $this->getAttr('working_shift_id')) {
            UpcomingDepartmentWorkingShift::query()
                ->where('department_id', $this->model->id)
                ->delete();

            UpcomingUserWorkingShift::query()->whereIn('user_id', $this->model->load('users')->users->pluck('id')->toArray())->delete();

            UpcomingDepartmentWorkingShift::query()->create([
                'working_shift_id' => $this->getAttr('working_shift_id'),
                'department_id' => $this->model->id,
                'start_date' => WorkingShift::query()->find($this->getAttr('working_shift_id'))->start_date ?: todayFromApp()
            ]);
        }

        return $this;
    }

    public function deleteUpcomingAssignWorkShift($department): self
    {
        $department->upcomingWorkingShift()->delete();

        return $this;
    }
}
