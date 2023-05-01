<?php


namespace App\Models\Tenant\Employee\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\Announcement\Announcement;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\Employee\DepartmentUser;
use App\Models\Tenant\Employee\Designation;
use App\Models\Tenant\WorkingShift\DepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\UpcomingDepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Models\Tenant\Holiday\Holiday;

trait DepartmentRelationship
{
    use StatusRelationship;

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'department_user',
            'department_id',
            'user_id'
        )->using(DepartmentUser::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

    public function designations()
    {
        return $this->hasMany(Designation::class, 'department_id');
    }

    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function parentDepartments()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id')
            ->with('parentDepartments:id,name,manager_id,department_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'id', 'department_id');
    }

    public function childDepartments()
    {
        return $this->hasMany(Department::class, 'department_id')
            ->with(
                'users:id',
                'childDepartments:id,name,manager_id,department_id',
                'childDepartments.manager:id,first_name,last_name,email',
            );
    }

    public function workingShift()
    {
        return $this->workingShifts()
            ->toOne()
            ->wherePivotNull('end_date')
            ->withPivot('start_date', 'end_date');
    }

    public function workingShifts()
    {
        return $this->belongsToMany(
            WorkingShift::class,
            'department_working_shift',
            'department_id',
            'working_shift_id'
        )->withPivot('start_date', 'end_date')
            ->using(DepartmentWorkingShift::class);
    }

    public function holidays()
    {
        return $this->belongsToMany(
            Holiday::class,
            'holiday_department',
            'department_id',
            'holiday_id'
        );
    }

    public function upcomingWorkingShift()
    {
        return $this->hasMany(UpcomingDepartmentWorkingShift::class, 'department_id', 'id');
    }

    public function announcements()
    {
        return $this->belongsToMany(
            Announcement::class,
            'announcement_department',
            'department_id',
            'announcement_id',
        );
    }
}
