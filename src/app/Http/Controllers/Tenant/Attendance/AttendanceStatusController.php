<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Filters\Tenant\AttendanceRequestsFilter;
use App\Helpers\Traits\DepartmentAuthentications;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceStatusService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceStatusController extends Controller
{
    use DepartmentAuthentications;

    public function __construct(AttendanceStatusService $service, AttendanceRequestsFilter $filter)
    {
        $this->service = $service;
        $this->filter = $filter;
    }

    public function update(AttendanceDetails $details, Request $request)
    {
        $this->service->validStatus($request->get('status_name'));

        $method = 'attendance' . ucfirst($request->get('status_name'));

        $status_id = resolve(StatusRepository::class)->$method();

        $details = $details->load('status', 'parentAttendanceDetails', 'attendance');

        if (!($request->get('status_name') == 'cancel' &&
            $details->status->name == 'status_pending' && $details->attendance->user->id == auth()->id())) {
            $this->departmentAuthentications($details->attendance->user->id);
        }

        DB::transaction(function () use ($details, $status_id, $request) {
            $this->service
                ->setDetails($details)
                ->setAttributes([
                    'status_id' => $status_id,
                    'requestedStatus' => $request->get('status_name'),
                    'previousStatus' => $details->status->name,
                    'review_by' => auth()->id()
                ])
                ->setModel($details->attendance->user)
                ->updateAttendanceDetailsStatus();
        });

        return [
            'status' => true,
            'message' => trans('default.status_updated_response', [
                'name' => __t('attendance'),
                'status' => $request->get('status_name')
            ])
        ];
    }

    public function updateAll(Request $request)
    {
        $method = 'attendance' . ucfirst($request->get('status'));
        $status_id = resolve(StatusRepository::class)->$method();
        $pending_status = resolve(StatusRepository::class)->attendancePending();

        $attendances = Attendance::filters($this->filter)->with([
            'user:id',
            'details' => fn(HasMany $hasMany) => $hasMany
                ->select('id', 'in_time', 'out_time', 'attendance_id', 'status_id', 'review_by', 'attendance_details_id')
                ->orderBy('id', 'DESC')
                ->where('status_id', $pending_status),
            'details.attendance:id,user_id,working_shift_id',
            'details.parentAttendanceDetails'
        ])->whereHas('details', fn(Builder $query) => $query->where('status_id', $pending_status))
            ->when(!$request->all_selected, fn(Builder $query) => $query->whereIn('id', $request->requests))
            ->get();

        $attendances->each(function (Attendance $attendance) use ($status_id) {
            if ($attendance->user_id != auth()->id()) {
                $attendance->details->each(function (AttendanceDetails $attendanceDetails) use ($status_id, $attendance) {
                    $this->service
                        ->setDetails($attendanceDetails)
                        ->setAttributes([
                            'status_id' => $status_id,
                            'requestedStatus' => \request()->get('status'),
                            'previousStatus' => $attendanceDetails->status->name,
                            'review_by' => auth()->id()
                        ])
                        ->setModel($attendance->user)
                        ->updateAttendanceDetailsStatus();
                });
            }
        });

        return [
            'status' => true,
            'message' => trans('default.status_updated_response', [
                'name' => __t('attendance'),
                'status' => $request->get('status')
            ])
        ];
    }
}
