<?php

namespace App\Services\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\HasWhen;
use App\Helpers\Traits\DepartmentAuthentications;
use App\Manager\Employee\EmployeeManager;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\User;
use App\Models\Tenant\WorkingShift\DepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Notifications\Core\User\UserNotification;
use App\Services\Core\Auth\UserService;
use App\Services\Tenant\TenantService;
use App\Services\Tenant\WorkingShift\WorkingShiftService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Types\Boolean;

class EmployeeService extends TenantService
{
    use HasWhen, DepartmentAuthentications;

    private EmployeeManager $manager;

    private $isInvite = false;

    public function __construct(User $user, EmployeeManager $manager)
    {
        $this->model = $user;

        $this->manager = $manager;
    }


    public function update()
    {
        DB::transaction(function () {
            $this->model->fill($this->getAttributes('first_name', 'last_name', 'email'));

            $this->when($this->model->isDirty(), fn (EmployeeService $service) =>  $service->notify('employee_updated'));

            $this->model->save();

            $this->saveEmployeeId();

            $this->assignToDepartment()
                ->assignToDesignation()
                ->assignToWorkShift()
                ->when(auth()->user()->can('attach_users_to_roles'),fn(EmployeeService $service) => $service
                    ->validateRoles()
                    ->checkAndSetIfDepartmentManager()
                    ->assignToRoles()
                )->assignEmploymentStatus();
        });

        return $this;
    }

    public function checkAndSetIfDepartmentManager(): self
    {
        $isDepartmentManager = $this->model->roles()
            ->where('alias', 'department_manager')->select('id')->first();

        if ($isDepartmentManager){
            $this->setAttr('roles', array_merge($this->getAttr('roles'), [$isDepartmentManager->id]));
        }

        return $this;
    }

    public function validateRoles(): self
    {
        validator($this->getAttributes(),[
            'roles' => [
                'required',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->whereIn('id', $this->getAttr('roles'));
                })
            ],
        ])->validate();

        return $this;
    }

    public function saveEmployeeId()
    {
        $this->when($this->getAttribute('employee_id'), function () {
            $this->model->profile()->updateOrCreate([
                'user_id' => $this->model->id
            ], [
                'user_id' => $this->model->id,
                'employee_id' => $this->getAttribute('employee_id'),
                'gender' => strtolower($this->getAttribute('gender'))
            ]);
        });

        return $this;
    }

    public function saveJoiningDate(): self
    {
        $this->when($this->getAttribute('joining_date'), function () {
            $this->model->profile()->updateOrCreate([
                'user_id' => $this->model->id
            ], [
                'user_id' => $this->model->id,
                'joining_date' => $this->getAttribute('joining_date')
            ]);
        });

        return $this;
    }

    public function saveSalary(): self
    {
        $this->when($this->getAttribute('salary'), function () {
            $this->model->salaries()->create([
                'user_id' => $this->model->id,
                'added_by' => auth()->id(),
                'amount' => $this->getAttribute('salary')
            ]);
        });

        return $this;
    }

    public function assignToRoles()
    {
        $this->when($this->getAttr('roles'), function (EmployeeService $service) {
            resolve(UserService::class)
                ->setModel($service->model)
                ->beforeAttachingRole()
                ->attachRole();
        });

        return $this;
    }

    public function assignRolesFromAttribute()
    {
        $this->model->roles()->sync($this->getAttr('roles'));

        return $this;
    }

    public function assignEmploymentStatus()
    {
        $this->when($this->getAttr('employment_status_id'), function (EmployeeService $service) {
            $service->manager
                ->walkInto('employmentStatus', $this->getAttr('employment_status_id'))
                ->setAttributes($this->getAttrs())
                ->assignEmployees($this->model->id);
        });

        return $this;
    }

    public function assignToWorkShift()
    {
        $this->when($this->getAttr('work_shift_id'), function (EmployeeService $service) {
            $service->manager
                ->walkInto('workShift', $this->getAttr('work_shift_id'))
                ->assignEmployees($this->model->id);
        });

        return $this;
    }

    public function assignToDesignation()
    {
        $this->when($this->getAttr('designation_id'), function (EmployeeService $service) {
            $service->manager
                ->walkInto('designation', $this->getAttr('designation_id'))
                ->assignEmployees($this->model->id);

        });

        return $this;
    }

    public function assignToDepartment()
    {
        $this->departmentAuthentications($this->getAttr('department_id'), false, 'department');

        $this->when($this->getAttr('department_id'), function(EmployeeService $service) {
            $service->manager
                ->walkInto('department', $this->getAttribute('department_id'))
                ->assignEmployees($this->model->id);
            
            $workingShiftId = DepartmentWorkingShift::getDepartmentWorkingShiftId($this->getAttr('department_id')) ?:
                WorkingShift::getDefault()->id;

            $this->when($workingShiftId && !$this->isInvite, function ($service) use ($workingShiftId) {
                resolve(WorkingShiftService::class)
                    ->setWorkShiftId((int) $workingShiftId)
                    ->departmentMoveChangeUpcomingWorkingShift([$this->model->id]);
                //->assignToUsers($this->model->id);
            });

            $this->when($workingShiftId && $this->isInvite, function ($service) use ($workingShiftId){
                resolve(WorkingShiftService::class)
                    ->setWorkShiftId((int) $workingShiftId)
                    ->assignToUsers($this->model->id);
            });

        });

        return $this;
    }

    public function notify($event, $model = null)
    {
        $model = $model ? $model : $this->model;

        notify()
            ->on($event)
            ->with($model)
            ->send(UserNotification::class);

        return $this;
    }

    public function assignToUpcomingWorkingShift()
    {
        $this->when($this->getAttr('work_shift_id'), function () {
            resolve(WorkingShiftService::class)
                ->setWorkShiftId((int) $this->getAttr('work_shift_id'))
                ->departmentMoveChangeUpcomingWorkingShift([$this->model->id]);
            //->assignToUsers($this->model->id);
        });
    }

    public function setIsInvite(bool $bool): self
    {
        $this->isInvite = $bool;

        return $this;
    }
}
