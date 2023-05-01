<?php

namespace App\Models\Tenant\Salary;

use App\Models\Tenant\Salary\Relationship\SalaryRelationship;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends TenantModel
{
    use HasFactory, SalaryRelationship;

    protected $fillable = [
        'user_id', 'amount', 'added_by', 'start_at', 'end_at'
    ];

}
