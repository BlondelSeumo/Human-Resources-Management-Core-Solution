<?php


namespace App\Models\Tenant\Traits;


use App\Models\Tenant\Employee\Department;

trait DepartmentRelationshipTrait
{
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
