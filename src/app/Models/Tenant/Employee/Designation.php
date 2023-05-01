<?php

namespace App\Models\Tenant\Employee;


use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Employee\Relationship\DesignationRelationship;
use App\Models\Tenant\Employee\Rules\DesignationRules;
use App\Models\Tenant\TenantModel;
use App\Models\Tenant\Traits\DepartmentRelationshipTrait;

class Designation extends TenantModel
{
    use StatusRelationship, DepartmentRelationshipTrait, DesignationRelationship , DesignationRules;

    protected $fillable = [
        'name', 'description', 'tenant_id', 'department_id'
    ];
}
