<?php


namespace App\Models\Tenant\Attendance\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\AttendanceDetails;
use App\Models\Tenant\Traits\CommentableTrait;
use App\Models\Tenant\WorkingShift\WorkingShift;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait AttendanceRelationship
{
    use CommentableTrait;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id', 'id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(AttendanceDetails::class);
    }

    public function workingShift(): BelongsTo
    {
        return $this->belongsTo(WorkingShift::class);
    }

}
