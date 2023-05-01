<?php

namespace App\Http\Controllers\Tenant\Leave;

use App\Filters\Tenant\LeaveStatusFilter;
use App\Helpers\Traits\DepartmentAuthentications;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Leave\Leave;
use App\Repositories\Core\Status\StatusRepository;
use App\Repositories\Tenant\Employee\DepartmentRepository;
use App\Services\Tenant\Leave\LeaveStatusService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveStatusController extends Controller
{
    use DepartmentAuthentications;

    public function __construct(LeaveStatusService $service, LeaveStatusFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function update(Leave $leave)
    {
        $status = request()->get('status_name');

        DB::transaction(function () use ($leave, $status) {
            $this->service
                ->setModel($leave->user->load('department'))
                ->setStatusName($status)
                ->setLeave($leave->load('lastStatus', 'status'))
                ->setSettings()
                ->setAttr('note', request()->get('note'))
                ->setStatusAttr()
                ->setDepartment()
                ->validationsAndSetCredentials()
                ->validateManger()
                ->addLeaveReview()
                ->when($this->service->isNeedToUpdateLeave, function (LeaveStatusService $service) {
                    $service->updateLeaveStatus();
                })->sendNotification($this->service->leave);
        });

        return updated_responses('leave');
    }

    public function updateAll(Request $request)
    {
        $request->validate([
            'note' => 'required'
        ]);
        $status_id = resolve(StatusRepository::class)->leavePending();
        $leave_requests = Leave::filters($this->filter)
            ->with([
                'user:id',
                'user.department',
                'lastStatus',
                'status',
                'lastReview',
                'lastReview.department:id,manager_id',
            ])
            ->where('status_id', $status_id)
            ->when(!$request->all_selected, fn(Builder $query) => $query->whereIn('id', $request->requests))
            ->get();
        $manager_dept = [];
        if (request()->get('access_behavior') == 'own_departments') {
            $manager_dept = resolve(DepartmentRepository::class)->getDepartments(auth()->id());
        }
        $leave_requests->each(function (Leave $leave) use ($request, $manager_dept) {
            if ($leave->user->id != auth()->id() && $this->canUpdate($leave, $manager_dept)) {
                $this->service
                    ->setModel($leave->user)
                    ->setStatusName($request->status)
                    ->setLeave($leave)
                    ->setSettings()
                    ->setAttr('note', $request->note)
                    ->setStatusAttr()
                    ->setDepartment()
                    ->validationsAndSetCredentials()
                    ->validateManger()
                    ->addLeaveReview()
                    ->when($this->service->isNeedToUpdateLeave, function (LeaveStatusService $service) {
                        $service->updateLeaveStatus();
                    })
                    ->sendNotification($this->service->leave);
            }
        });

        return updated_responses('leave');
    }

    public function canUpdate($leave, $manager_dept): bool
    {
        if (count($manager_dept) < 1 || $leave->lastReview->count() < 1) {
            return true;
        }
        return in_array(optional($leave->lastReview->first())->department_id, $manager_dept);
    }
}
