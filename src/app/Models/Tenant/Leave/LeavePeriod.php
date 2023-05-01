<?php


namespace App\Models\Tenant\Leave;


use App\Models\Tenant\TenantModel;

class LeavePeriod extends TenantModel
{
    protected $fillable = [
        'start_date', 'end_date', 'tenant_id'
    ];

    protected $dates = [
        'start_date', 'end_date'
    ];
}
