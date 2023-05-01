<?php

namespace App\Models\Tenant\Attendance;

use App\Models\Tenant\Attendance\Relationship\AttendanceRelationship;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Builder;

class Attendance extends TenantModel
{
    use AttendanceRelationship;

    protected $fillable = [
        'in_date', 'user_id', 'status_id', 'tenant_id', 'working_shift_id', 'behavior'
    ];

    public static array $statuses = [
        'approve', 'reject', 'cancel'
    ];

    public function scopeDate(Builder $query, $date)
    {
        return $query->whereDate('in_date', $date);
    }

}
