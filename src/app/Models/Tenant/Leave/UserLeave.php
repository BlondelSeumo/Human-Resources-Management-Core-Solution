<?php

namespace App\Models\Tenant\Leave;

use App\Models\Tenant\Leave\Relationship\UserLeaveRelationship;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLeave extends TenantModel
{
    use HasFactory, UserLeaveRelationship;

    protected $fillable = [
        'user_id','leave_type_id','amount','start_date','end_date','is_updated'
    ];

}
