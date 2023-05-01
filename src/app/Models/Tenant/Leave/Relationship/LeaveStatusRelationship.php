<?php


namespace App\Models\Tenant\Leave\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Leave\Leave;
use App\Models\Tenant\Traits\CommentableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait LeaveStatusRelationship
{
    use  CommentableTrait, StatusRelationship;

    public function leave(): BelongsTo
    {
        return $this->belongsTo(Leave::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by', 'id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}