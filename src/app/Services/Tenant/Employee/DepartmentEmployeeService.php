<?php


namespace App\Services\Tenant\Employee;


use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\DepartmentUser;
use App\Models\Tenant\WorkingShift\DepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Services\Tenant\TenantService;
use App\Services\Tenant\WorkingShift\WorkingShiftService;

class DepartmentEmployeeService extends TenantService
{
    protected $department_id;

    public function __construct(Department $department)
    {
        $this->model = $department;
    }

    public function moveEmployee()
    {
        $this->endEmployeesPreviousDepartment()
            ->moveToDepartment();

        return $this;
    }

    public function endEmployeesPreviousDepartment()
    {
        DepartmentUser::whereIn('user_id', $this->getAttribute('users'))
            ->whereNull('end_date')
            ->where('department_id', '!=', $this->getDepartmentId())
            ->update([
                'end_date' => nowFromApp()->format('Y-m-d')
            ]);

        return $this;
    }

    public function moveToDepartment()
    {
        $department_users = collect(DepartmentUser::getNoneExistedUsers(
                $this->getDepartmentId(),
                $this->getAttribute('users')
            ))->map(fn ($user) => [
                'department_id' => $this->getDepartmentId(),
                'user_id' => $user,
                'start_date' => nowFromApp()->format('Y-m-d')
            ])->toArray();

        DepartmentUser::insert($department_users);

        $departmentWorkingShiftId = DepartmentWorkingShift::getDepartmentWorkingShiftId($this->getDepartmentId()) ?:
            WorkingShift::getDefault()->id;

        $this->assignUserToDepartmentWorkingShift($departmentWorkingShiftId, $this->getAttribute('users'));

        return $this;
    }

    public function assignUserToDepartmentWorkingShift($workingShiftId, $users = []): self
    {
        resolve(WorkingShiftService::class)
            ->setModel(WorkingShift::find($workingShiftId))
            ->departmentMoveChangeUpcomingWorkingShift($users);
            //->assignToUsers($users);

        return $this;
    }

    public function setDepartmentId($department_id): DepartmentEmployeeService
    {
        $this->department_id = $department_id;
        return $this;
    }

    public function getDepartmentId()
    {
        return $this->department_id ?: $this->model->id;
    }
}
