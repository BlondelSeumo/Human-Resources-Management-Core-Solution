<?php


namespace App\Models\Tenant\Leave\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Tenant\Leave\LeaveType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait UserLeaveRelationship
{

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}