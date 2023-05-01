<?php


namespace App\Models\Tenant\Employee;


use App\Models\Core\Auth\User;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Traits\HasAlias;

class EmploymentStatus extends TenantModel
{
    use HasAlias;

    public $timestamps = false;

    protected $fillable = [
        'name', 'class', 'description', 'tenant_id', 'alias'
    ];

    public function employees()
    {
        return $this->belongsToMany(User::class, 'user_employment_status','employment_status_id', 'user_id')
            ->withPivot('start_date', 'end_date', 'description');
    }
}
