<?php

namespace App\Services\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Mail\Tenant\EmployeeSalaryIncrementMail;
use App\Notifications\Tenant\SalaryIncrementNotification;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\Salary\SalaryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmployeeUpdateService extends EmployeeService
{
    protected array $ApprovedMethods = [
        'department' => 'assignToDepartment',
        'workshift' => 'assignToUpcomingWorkingShift',
        'designation' => 'assignToDesignation',
        'roles' => 'assignToRoles',
        'employment-status' => 'assignEmploymentStatus',
        'joining-date' => 'assignJoiningDate',
        'salary' => 'updateSalary'
    ];

    protected array $permissions = [
        'department' => 'move_department_employees',
        'workshift' => 'update_employees',
        'designation' => 'update_employees',
        'roles' => 'attach_roles_users',
        'employment-status' => 'update_employees',
        'joining-date' => 'update_employees',
        'salary' => 'update_salary'
    ];

    protected string $method;

    public function checkPermissions(): self
    {
        throw_if(
            !auth()->user()->can($this->permissions[$this->method]),
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

    public function setMethod($method): self
    {
        $this->method = $method;
        return $this;
    }

    public function validateMethod(): self
    {
        throw_if(
            !array_key_exists($this->method, $this->ApprovedMethods),
            new GeneralException(__t('action_not_allowed'))
        );

        return $this;
    }

    public function callMethod(): self
    {
        $this->{$this->ApprovedMethods[$this->method]}();

        return $this;
    }

    public function assignJoiningDate(): self
    {
        $this->when($this->getAttr('joining_date'), function (EmployeeUpdateService $service) {
            $service->model->profile->updateOrCreate([
                'user_id' => $service->model->id,
            ], [
                'joining_date' => $this->getAttr('joining_date')
            ]);
        });

        return $this;
    }

    public function updateSalary(): self
    {
        $hasSalary = $this->model->salary;

        DB::transaction(function () {
            resolve(SalaryService::class)
                ->setModel($this->model)
                ->setAttrs(request()->only(['amount', 'start_at']))
                ->validateAttributes()
                ->updateSalary();
        });

        if ($hasSalary) {
            $deptManagers = resolve(DepartmentRepository::class)
                ->getDepartmentsManagers($this->model->department->first()->id, 'parents', 'department');
            try {
                notify()
                    ->on('salary_increment')
                    ->with($this->model, $this->model->updatedSalary)
                    ->mergeAudiences($deptManagers)
                    ->send(SalaryIncrementNotification::class);

                Mail::to($this->model->email)
                    ->send(new EmployeeSalaryIncrementMail(
                        (object)$this->model->toArray(),
                        $this->model->updatedSalary,
                    ));
            } catch (\Exception $exception) { /* Ignore */ }
        }

        return $this;
    }

}
