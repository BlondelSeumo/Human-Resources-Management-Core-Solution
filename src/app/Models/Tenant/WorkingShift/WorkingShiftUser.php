<?php

namespace App\Models\Tenant\WorkingShift;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class WorkingShiftUser extends Pivot
{
    protected $primaryKey = false;

    public $timestamps = false;
    
    protected $fillable = [
        'start_date', 'end_date', 'working_shift_id', 'user_id'
    ];

    protected $dates = [
        'start_date', 'end_date'
    ];

    public static function getNoneExistedUserIds(int $workShiftId, array $users = []): array
    {
        $existed = self::query()
            ->where('working_shift_id', $workShiftId)
            ->whereNull('end_date')
            ->pluck('user_id')
            ->toArray();

        return array_filter($users, fn ($workShiftUser) => !in_array($workShiftUser, $existed));
    }

    public function workingShift(): BelongsTo
    {
        return $this->belongsTo(WorkingShift::class);
    }

}
