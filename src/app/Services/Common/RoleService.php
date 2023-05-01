<?php


namespace App\Services\Common;


use App\Models\App\Tenant\Tenant;
use App\Models\Core\Auth\Role;
use App\Models\Core\Auth\Type;
use App\Services\Core\BaseService;

class RoleService extends BaseService
{
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function create(Tenant $tenant)
    {
        $this->model->fill([
            'name' => 'Tenant Admin',
            'is_admin' => 1,
            'is_default' => 1,
            'tenant_id' => $tenant->id,
            'created_by' => $tenant->created_by,
            'type_id' => Type::findByAlias('tenant')->id,
        ])->save();

        return $this->model;
    }
}