<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Filters\Tenant\EmployeeFilter;
use App\Helpers\Traits\AssignRelationshipToPaginate;
use App\Helpers\Traits\DepartmentAuthentications;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\EmployeeRequest;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\EmploymentStatus;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Services\Core\Auth\UserService;
use App\Services\Tenant\Employee\EmployeeService;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    use AssignRelationshipToPaginate, DepartmentAuthentications;

    public function __construct(EmployeeService $service, EmployeeFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        $workShift = WorkingShift::getDefault(['id', 'name']);

        if (!request()->get('employment_statuses')) {
            request()->merge(['employment_statuses' => implode(', ', EmploymentStatus::query()
                ->where('alias', '!=', 'terminated')
                ->pluck('id')
                ->toArray())
            ]);
        }

        $paginated = $this->service
            ->filters($this->filter)
            ->with([
                'department:id,name',
                'designation:id,name',
                'profile:id,user_id,joining_date,employee_id',
                'profilePicture',
                'workingShift:id,name',
                'employmentStatus:id,name,class,alias',
                'roles:id,name',
                'status',
                'updatedSalary',
                'salary'
            ])->where('is_in_employee', 1)
            ->latest('id')
            ->paginate(request()->get('per_page', 10));

        return $this->paginated($paginated)
            ->setRelation(function (User $user) use ($workShift) {
                if (!$user->workingShift) {
                    $user->setRelation('workingShift', $workShift);
                }
            })->get();
    }

    public function show(User $employee)
    {
         $employee->load([
            'department:id,name',
            'designation:id,name',
            'profile:id,user_id,joining_date,employee_id,gender,date_of_birth,about_me,phone_number',
            'profilePicture',
            'workingShift:id,name',
            'employmentStatus:id,name,class,alias',
            'roles:id,name',
            'status',
            'updatedSalary',
            'salary'
        ]);
        if (!$employee->workingShift) {
            $workShift = WorkingShift::getDefault(['id', 'name', 'is_default']);
            $employee->setRelation('workingShift', $workShift);
        }
        return $employee;
    }

    public function update(EmployeeRequest $request, User $employee)
    {
        $this->departmentAuthentications($employee->id);

        resolve(UserService::class)->validateIsNotDemoVersion();

        DB::transaction(function () use($employee, $request){
            $this->service
                ->setModel($employee)
                ->setAttributes($request->except('allowed_resource', 'tenant_id', 'tenant_short_name'))
                ->update();
        });

        return updated_responses('employee');
    }

    public function destroy(User $employee)
    {
        $this->service
            ->setModel($employee)
            ->delete();

        return deleted_responses('employee');
    }

}
