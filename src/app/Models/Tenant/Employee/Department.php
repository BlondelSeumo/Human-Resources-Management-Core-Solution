<?php


namespace App\Models\Tenant\Employee;


use App\Hooks\Department\DepartmentCreated;
use App\Hooks\Department\DepartmentUpdated;
use App\Models\Tenant\Employee\Methods\DepartmentMethods;
use App\Models\Tenant\Employee\Relationship\DepartmentRelationship;
use App\Models\Tenant\Employee\Scopes\DepartmentScopes;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class Department extends TenantModel
{
    use DepartmentRelationship, DepartmentScopes, DepartmentMethods;

    protected $fillable = [
        'name', 'description', 'location', 'department_id', 'tenant_id', 'manager_id', 'status_id'
    ];

    /**
     * @return Model|Builder|object|null|Department
     */
    public static function main()
    {
        return self::whereNull('department_id')->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function (Department $department) {
            DepartmentCreated::new()
                ->setModel($department)
                ->handle();
        });

        static::updated(function (Department $department) {
            DepartmentUpdated::new()
                ->setModel($department)
                ->handle();
        });
    }

}
