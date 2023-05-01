<?php


namespace App\Models\Tenant\Holiday\Relationship;


use App\Models\Tenant\Employee\Department;

trait HolidayRelationship
{
    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'holiday_department',
            'holiday_id',
            'department_id'
        );
    }
}
