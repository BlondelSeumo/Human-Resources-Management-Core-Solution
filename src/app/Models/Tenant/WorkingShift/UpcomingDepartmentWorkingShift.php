<?php

namespace App\Models\Tenant\WorkingShift;

use App\Models\Tenant\Employee\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpcomingDepartmentWorkingShift extends Model
{
    protected $fillable = ['department_id', 'working_shift_id', 'start_date'];

    use HasFactory;

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function workingShift()
    {
        return $this->belongsTo(WorkingShift::class);
    }
}
