<?php


namespace App\Models\Tenant\WorkingShift\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Tenant\Attendance\Attendance;
use App\Models\Tenant\Employee\Department;
use App\Models\Tenant\WorkingShift\UpcomingDepartmentWorkingShift;
use App\Models\Tenant\WorkingShift\WorkingShiftDetails;
use App\Models\Tenant\WorkingShift\WorkingShiftUser;

trait   WorkingShiftRelationShip
{
    public function details()
    {
        return $this->hasMany(WorkingShiftDetails::class);
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'working_shift_user',
            'working_shift_id',
            'user_id'
        )->using(WorkingShiftUser::class)
            ->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'department_working_shift'
        )->withPivot('start_date', 'end_date')
            ->wherePivotNull('end_date');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function upcomingDepartments()
    {
        return $this->belongsToMany(
          Department::class,
            'upcoming_department_working_shifts',
            'working_shift_id'
        );
    }

    public function upcomingUsers()
    {
        return $this->belongsToMany(
            User::class,
            'upcoming_user_working_shifts',
            'working_shift_id'
        );
    }

}
