<?php


namespace App\Services\Tenant\Utility;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Models\Core\Auth\Permission;
use App\Models\Core\Auth\Role;
use App\Services\Tenant\TenantService;

class TenantRoleCreationService extends TenantService
{
    use InstanceCreator;

    public function trigger($tenant, $type)
    {
        $manager = new Role();

        $manager->fill([
            'name' => 'Manager',
            'is_admin' => 0,
            'is_default' => 1,
            'tenant_id' => $tenant->id,
            'created_by' => $tenant->created_by,
            'type_id' => $type->id,
            'alias' => 'manager'
        ])->save();

        $manager->permissions()->sync(
            Permission::whereNull('type_id')
                ->orWhere('type_id', $type->id)
                ->pluck('id')
                ->toArray()
        );

        $employee = new Role();

        $employee->fill([
            'name' => 'Employee',
            'is_admin' => 0,
            'is_default' => 0,
            'tenant_id' => $tenant->id,
            'created_by' => $tenant->created_by,
            'type_id' => $type->id,
            'alias' => 'employee'
        ])->save();

        return ['manager' => $manager, 'employee' => $employee];
    }
}