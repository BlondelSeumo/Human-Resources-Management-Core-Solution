<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Filters\Tenant\AttendanceDailLogFilter;
use App\Helpers\Traits\TenantAble;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Attendance\Attendance;
use App\Repositories\Core\Setting\SettingRepository;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AttendanceDailyLogController extends Controller
{
    use TenantAble;

    protected SettingRepository $repository;

    public function __construct(AttendanceDailLogFilter $filter, SettingRepository $repository)
    {
        $this->filter = $filter;
        $this->repository = $repository;
    }

    public function index()
    {
        $attendanceApprove = resolve(StatusRepository::class)->attendanceApprove();

        return Attendance::filters($this->filter)->with([
            'user:id,first_name,last_name,status_id',
            'user.department:id,name',
            'user.profilePicture',
            'user.status:id,name,class',
            'details.status:name,id',
            'details' => fn(HasMany $hasMany) => $hasMany
                ->select(
                    'id',
                    'in_time',
                    'out_time',
                    'attendance_id',
                    'status_id',
                    'review_by',
                    'added_by',
                    'attendance_details_id',
                    'in_ip_data',
                    'out_ip_data'
                )
                ->where('status_id', $attendanceApprove)
                ->orderBy('in_time', 'DESC'),
            'details.comments' => fn(MorphMany $many) => $many->orderBy('parent_id', 'DESC')
                ->select('id','commentable_type','commentable_id','user_id','type','comment', 'parent_id')
        ])->where('status_id', $attendanceApprove)
            ->latest()
            ->paginate(request()->get('per_page', 15));
    }
}
