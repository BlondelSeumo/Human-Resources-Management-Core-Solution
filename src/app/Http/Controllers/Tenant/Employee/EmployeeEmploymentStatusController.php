<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Helpers\Core\Traits\HasWhen;
use App\Helpers\Traits\DepartmentAuthentications;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\EmployeeStatusRequest;
use App\Mail\Tenant\EmployeeTerminateMail;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Notifications\Tenant\EmployeeTerminateNotification;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\Employee\EmployeeEmploymentStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmployeeEmploymentStatusController extends Controller
{
    use HasWhen, DepartmentAuthentications;

    public function __construct(EmployeeEmploymentStatusService $service)
    {
        $this->service = $service;
    }

    public function terminate(User $employee, Request $request)
    {
        $this->departmentAuthentications($employee->id);

        throw_if(
            $employee->isInvited() || $employee->isInactive(),
            new GeneralException(__t('action_not_allowed'))
        );

        $terminateStatus = EmploymentStatus::getByAlias('terminated');

        $this->service
            ->setModel($terminateStatus)
            ->setAttributes($request->only('description'))
            ->changeStatus($employee, 'inactive');

//        try {
//            Mail::to($employee->email)
//                ->send(new EmployeeTerminateMail((object)$employee->toArray()));
//        } catch (\Exception $exception) { /* Ignore */ }

        try {
            $deptManagers = resolve(DepartmentRepository::class)
                ->getDepartmentsManagers($employee->department->first()->id, 'parents', 'department');

            notify()
                ->on('employee_termination')
                ->with($employee)
                ->mergeAudiences($deptManagers)
                ->send(EmployeeTerminateNotification::class);
        } catch (\Exception $exception) { /* Ignore */}

        return [
            'status' => true,
            'message' => trans('default.status_updated_response', [
                'name' => __t('employee'),
                'status' => $terminateStatus->name
            ])
        ];

    }

    public function rejoin(User $employee, Request $request)
    {
        $this->departmentAuthentications($employee->id);

        $request->validate([
            'employment_status_id' => 'required'
        ]);

        throw_if(
            !$employee->isTerminated(),
            new GeneralException(__t('action_not_allowed'))
        );

        $employmentStatus = EmploymentStatus::find($request->get('employment_status_id'));

        throw_if(
            !$employmentStatus,
            new GeneralException(__t('action_not_allowed'))
        );

        $this->service
            ->setModel($employmentStatus)
            ->setAttributes($request->only('description'))
            ->changeStatus($employee, 'active');

        return [
            'status' => true,
            'message' => trans('default.status_updated_response', [
                'name' => __t('employee'),
                'status' => $employmentStatus->name
            ])
        ];

    }

    public function updateTerminationNote(User $employee, Request $request)
    {
        $employee->load('employmentStatus');

        throw_if(
            !$employee->isTerminated(),
            new GeneralException(__t('action_not_allowed'))
        );

        $this->service
            ->setAttributes($request->only('description'))
            ->validateDescription()
            ->updateNote($employee);

        return updated_responses('termination_reason');
    }

    public function update(EmployeeStatusRequest $request, User $employee, $status)
    {
        $this->service
            ->setEmploymentStatusId($status)
            ->setAttributes([
                'description' => $request->get('description')
            ])
            ->endPreviousEmploymentStatus($employee->id)
            ->assignToUsers($employee->id);

        return [
            'status' => true,
            'message' => trans('default.status_updated_response', [
                'name' => __t('employee'),
                'status' => $employee->load('employmentStatus')->employmentStatus->name
            ])
        ];
    }
}

