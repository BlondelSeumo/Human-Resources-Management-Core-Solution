<?php

namespace App\Models\Tenant\WorkingShift;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DepartmentWorkingShift extends Pivot
{
    protected $table = 'department_working_shift';

    public $timestamps = false;

    protected $primaryKey = false;

    protected $fillable = ['department_id', 'working_shift_id', 'start_date', 'end_date'];

    public static function getNonExistDepartmentIds(int $workingsShiftId, array $departments = []): array
    {
        $existed = self::query()
            ->where('working_shift_id', $workingsShiftId)
            ->whereNull('end_date')
            ->pluck('department_id')
            ->toArray();

        return array_filter($departments, fn ($department) => !in_array($department, $existed));
    }

    public static function getDepartmentWorkingShiftId($department_id)
    {
        $workingShift = self::query()
            ->select('working_shift_id')
            ->where('department_id', $department_id)
            ->whereNull('end_date')
            ->first();

        return optional($workingShift)->working_shift_id;
    }

}
