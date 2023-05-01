<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Attendance\AttendanceDetails;

class AttendanceLogController extends Controller
{
    public function index(AttendanceDetails $details): AttendanceDetails
    {
        return $details->load(
            'parentAttendanceDetails:id,in_time,out_time,status_id,review_by,attendance_details_id,created_at,updated_at,added_by',
            'comments',
            'comments.user:id,first_name,last_name',
            'status:id,name',
            'reviewer:id,first_name,last_name',
            'attendance:id,user_id',
            'attendance.user:id,first_name,last_name',
            'assignBy:id,first_name,last_name'
        );
    }
}
