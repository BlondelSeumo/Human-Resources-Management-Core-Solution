<?php

namespace App\Models\Tenant\Employee;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserEmploymentStatus extends Pivot
{
    protected $primaryKey = false;

    public $timestamps = false;

    protected $fillable = ['user_id', 'employment_status_id', 'start_date', 'end_date', 'description'];


    public static function getNoneExistedUsers(int $employmentStatusId, array $users = []): array
    {
        $existed = self::query()
            ->where('employment_status_id', $employmentStatusId)
            ->whereNull('end_date')
            ->pluck('user_id')
            ->toArray();

        return array_filter($users, fn($user) => !in_array($user, $existed));
    }
}
