<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Helpers\Traits\DepartmentAuthentications;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Services\Tenant\Attendance\AttendanceUpdateService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceUpdateController extends Controller
{
    use DepartmentAuthentications;

    public function __construct(AttendanceUpdateService $service)
    {
        $this->service = $service;
    }

    public function index(AttendanceDetails $attendanceDetails)
    {
        return $attendanceDetails->load([
            'comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                ->select('id', 'commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id')
        ]);
    }

    public function request(Request $request, AttendanceDetails $attendanceDetails)
    {
        $attendanceDetails->load('attendance');

        $this->departmentAuthentications($attendanceDetails->attendance->user->id, true);

        DB::transaction(
            fn () => $this->service
                ->setModel($attendanceDetails->attendance->user)
                ->setAttributes($request->only('in_time', 'out_time', 'reason', 'note', 'in_ip_data', 'out_ip_data'))
                ->mergeAttributes($this->service->getStatusAttribute())
                ->setDetails($attendanceDetails)
                ->validateIfAlreadyRequested($attendanceDetails->id)
                ->validateForRequest()
                ->validateIfNotFuture()
                ->validateAttendanceRequestDate()
                ->validateWorkShift()
                ->validateOwner()
                ->validateExistingPunchTime($attendanceDetails->id)
                ->validateIfApproved()
                ->duplicate($attendanceDetails->attendance)
        );

        return response()->json([
            'status' => true,
            'message' => __t('attendance_request_has_been_sent_successfully')
        ]);
    }
}
