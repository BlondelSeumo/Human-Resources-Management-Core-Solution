<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Filters\Tenant\DepartmentFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\DepartmentRequest;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\WorkingShift\UpcomingDepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Employee\DepartmentService;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function __construct(DepartmentService $service, DepartmentFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->with('status:id,name,class', 'manager:id,first_name,last_name', 'parentDepartment:id,name')
            ->latest('id')
            ->paginate(request()->get('per_page', 10));
    }

    public function store(DepartmentRequest $request)
    {
        $department = DB::transaction(function () use ($request) {
            $status = resolve(StatusRepository::class)->departmentActive();

            $attributes = array_merge($request
                ->only('name', 'manager_id', 'department_id', 'location', 'description','working_shift_id'),
                [
                    'status_id' => $status
                ]
            );

            $department = $this->service
                ->setAttributes($attributes)
                ->validateParent()
                ->save();
            $this->service
                ->setModel($department)
                ->makeUserDepartmentManager()
                ->assignWorkingShift()
                ->notify('department_created');

            return $department;
        });

        return created_responses('department', ['department' => $department]);
    }

    public function show(Department $department)
    {
        $workShift = WorkingShift::getDefault(['id']);
        $department = $department->load('status', 'manager','workingShift','upcomingWorkingShift', 'upcomingWorkingShift.workingShift:id,name');

        if (!$department->workingShift){
            $department->setRelation('workingShift', $workShift);
        }

        return $department;
    }

    public function update(Department $department, DepartmentRequest $request)
    {
        $department = DB::transaction(function () use ($request, $department) {
            $this->service
                ->setCurrentManager($department->manager_id)
                ->setAttributes($request
                    ->only('name', 'manager_id', 'department_id', 'location', 'description', 'working_shift_id'))
                ->setModel($department)
                ->validateParent()
                ->validateForParentDepartments()
                ->update()
                ->setIsUpdate(true)
                ->assignUpcomingWorkshift();
               // ->assignWorkingShift();
        });

        return updated_responses('department', ['department' => $department]);
    }

    public function destroy(Department $department)
    {
        $this->service
            ->setModel($department)
            ->delete()
            ->deleteAssignWorkShift($department)
            ->deleteUpcomingAssignWorkShift($department)
            ->notify('department_deleted', (object)$department->toArray());

        return deleted_responses('department');
    }

    public function deleteUpcomingWorkShift($id)
    {
        $departmentWorkingShift = UpcomingDepartmentWorkingShift::findOrFail($id);
        $departmentWorkingShift->delete();

        return response()->json(['status' => true, 'message' => __t('working_shift_removed_from_department') ], 200);
    }
}
