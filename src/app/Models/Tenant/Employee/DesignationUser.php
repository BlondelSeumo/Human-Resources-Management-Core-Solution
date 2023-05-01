<?php


namespace App\Models\Tenant\Employee;


use Illuminate\Database\Eloquent\Relations\Pivot;

class DesignationUser extends Pivot
{
    protected $primaryKey = false;

    public $timestamps = false;

    protected $fillable = [
        'start_date', 'end_date', 'designation_id', 'user_id'
    ];

    protected $dates = [
        'start_date', 'end_date'
    ];

    public static function getNoneExistedUsers(int $designationId, array $users): array
    {
        $existed = self::query()
            ->where('designation_id', $designationId)
            ->whereNull('end_date')
            ->pluck('user_id')
            ->toArray();

        return array_filter($users, fn($user) => !in_array($user, $existed));
    }
}
