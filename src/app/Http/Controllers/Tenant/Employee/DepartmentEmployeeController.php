<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Employee\Department;
use App\Services\Tenant\Employee\DepartmentEmployeeService;
use App\Services\Tenant\WorkingShift\WorkingShiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentEmployeeController extends Controller
{
    protected WorkingShiftService $workingShiftService;

    public function __construct(DepartmentEmployeeService $service, WorkingShiftService $workingShiftService)
    {
        $this->service = $service;
        $this->workingShiftService = $workingShiftService;
    }

    public function getEmployees(Department $department) {
        return $department->users()
            ->with(['profilePicture', 'status:id,name,class'])
            ->get();
    }

    public function update(Request $request)
    {
        validator($request->all(), [
            'department_id' => 'required|exists:departments,id',
            'users' => 'required|array'
        ])->validate();

        $department = Department::with('workingShift')->findOrFail($request->get('department_id'));

        DB::transaction(function() use($department, $request) {
            $this->service
                ->setModel($department)
                ->setAttributes($request->only('department_id', 'users'))
                ->moveEmployee();

//            $this->workingShiftService
//                ->when($department->workingShift, function (WorkingShiftService $service, $workingShift) use ($request) {
//                    $service
//                        ->setModel($workingShift)
//                        ->assignToUsers($request->get('users'));
//                });
        });

        return [
            'status' => true,
            'message' => trans('default.move_response', [
                'subject' => __t('employee'),
                'location' => $department->name
            ])
        ];
    }
}
