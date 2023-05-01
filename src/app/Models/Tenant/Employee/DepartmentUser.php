<?php


namespace App\Models\Tenant\Employee;


use App\Models\Tenant\Traits\DepartmentRelationshipTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DepartmentUser extends Pivot
{
    use DepartmentRelationshipTrait;

    protected $fillable = [
        'start_date', 'end_date', 'department_id', 'user_id'
    ];

    public $timestamps = false;

    protected $dates = [
        'start_date', 'end_date'
    ];

    public static function getNoneExistedUsers(int $department_id, array $users): array
    {
        $existed = self::query()
            ->where('department_id', $department_id)
            ->whereNull('end_date')
            ->pluck('user_id')
            ->toArray();

        return array_filter($users, fn ($user) => !in_array($user, $existed));
    }
}
