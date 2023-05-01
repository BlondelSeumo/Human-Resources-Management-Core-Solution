<?php

namespace App\Models\Tenant\Leave;

use App\Models\Tenant\Leave\Relationship\LeaveStatusRelationship;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeaveStatus extends TenantModel
{
    use HasFactory, LeaveStatusRelationship;

    protected $fillable = [
        'leave_id', 'reviewed_by', 'status_id', 'department_id', 'is_last'
    ];

    protected $table = 'leave_statuses';

}
