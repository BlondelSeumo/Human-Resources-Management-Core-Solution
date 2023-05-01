<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Helpers\Traits\DepartmentAuthentications;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManualAttendanceController extends Controller
{
    use DepartmentAuthentications;

    public function __construct(AttendanceService $service)
    {
        $this->service = $service;
    }

    public function store(Request $request)
    {
        $this->departmentAuthentications($request->get('employee_id'), true);

        $this->service
            ->setAttributes(
                array_merge($request->only('employee_id', 'in_date', 'note', 'in_time', 'out_time'), [
                    'review_by' => auth()->id(),
                ])
            )
            ->validateManual()
            ->validateIfNotFuture();

        $employee = User::find($request->get('employee_id'));

        $this->service->setModel($employee);

        $status_name = $this->service->autoApproval() ? 'approve' : 'pending';
        $status_methods = 'attendance' . ucfirst($status_name);
        $status = resolve(StatusRepository::class)->$status_methods();

        DB::transaction(function () use ($employee, $status_name, $status){
            $this->service
                ->setAttr('status_id', $status)
                ->setAttr('note_type', $status_name == 'approve' ? 'manual' : 'request')
                ->when($status_name == 'approve',
                    fn(AttendanceService $service) => $service->setAttr('review_by', auth()->id())
                )->setAttr('added_by', auth()->id())
                ->manualAddPunch()
                ->when($status_name == 'approve',
                    function (AttendanceService $service) use ($status) {
                        $attributes = ['status_id' => $status];

                        if(!$service->isNotFirstAttendance()){
                            $attributes = array_merge([
                                'behavior' => $service->getUpdateBehavior()
                            ], $attributes) ;
                        }

                        $service->updateAttendance($attributes);
                    }
                );
        });

        return response()->json([
            'status' => true,
            'message' => __('default.added_response', ['subject' => __t('attendance'), 'object' => $employee->full_name])
        ]);

    }
}
