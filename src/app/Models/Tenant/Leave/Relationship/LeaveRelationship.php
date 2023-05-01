<?php


namespace App\Models\Tenant\Leave\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Core\File\File;
use App\Models\Core\Status;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Leave\LeaveStatus;
use App\Models\Tenant\Leave\LeaveType;
use App\Models\Tenant\Traits\CommentableTrait;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait LeaveRelationship
{
    use StatusRelationship, CommentableTrait;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'id');
    }

    public function lastStatus()
    {
        return $this->statuses()
            ->toOne()
            ->withPivot('reviewed_by', 'created_at', 'updated_at', 'department_id')
            ->orderBy('pivot_created_at', 'DESC');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(LeaveStatus::class, 'leave_id');
    }

    public function lastReview(): HasMany
    {
        return $this->reviews()
            ->where('is_last', true);
    }

    public function statuses(): BelongsToMany
    {
        return $this->belongsToMany(Status::class, 'leave_statuses')
            ->withPivot('reviewed_by', 'created_at', 'updated_at');
    }

    public function workingShiftDetails(): BelongsTo
    {
        return $this->belongsTo(WorkingShiftDetails::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }
}