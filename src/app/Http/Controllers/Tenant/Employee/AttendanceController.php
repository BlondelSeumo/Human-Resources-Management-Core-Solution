<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\Attendance\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function __construct(AttendanceService $service)
    {
        activity()->disableLogging();

        $this->service = $service;
    }

    public function punchIn(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $status = resolve(StatusRepository::class)->attendanceApprove();
        DB::transaction(function() use($user, $request, $status) {
            $this->service
                ->setModel($user)
                ->setAttributes(array_merge($request->only('note', 'today', 'ip_data'),['status_id' => $status, 'punch_in' => true]))
                ->validatePunchInDate($request->get('today'))
                ->punchIn();
        });

        return response()->json([
            'status' => true,
            'message' => __t('punched_in_successfully')
        ]);
    }

    public function punchOut(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $status = resolve(StatusRepository::class)->attendanceApprove();

        DB::transaction(
            function() use($user, $request, $status) {
                $this->service
                    ->setModel($user)
                    ->setAttributes(array_merge($request->only('note', 'ip_data'),['status_id' => $status, 'punch_in' => false]))
                    ->punchOut();
            }
        );

        return response()->json([
            'status' => true,
            'message' => __t('punched_out_successfully')
        ]);
    }

}
