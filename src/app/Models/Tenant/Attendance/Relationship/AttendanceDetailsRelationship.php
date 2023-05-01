<?php


namespace App\Models\Tenant\Attendance\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Core\Status;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Traits\CommentableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait AttendanceDetailsRelationship
{
    use CommentableTrait, StatusRelationship;

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function parentAttendanceDetails(): BelongsTo
    {
        return $this->belongsTo(AttendanceDetails::class, 'attendance_details_id', 'id')
            ->with(
                'parentAttendanceDetails:id,in_time,out_time,status_id,review_by,added_by,attendance_details_id,created_at,updated_at',
                'comments',
                'comments.user:id,first_name,last_name',
                'status:id,name',
                'reviewer:id,first_name,last_name',
                'assignBy:id,first_name,last_name'
            );
    }

    public function attendanceDetails(): HasMany
    {
        return $this->hasMany(AttendanceDetails::class, 'id', 'attendance_details_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'review_by', 'id');
    }

    public function assignBy(): belongsTo
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
}