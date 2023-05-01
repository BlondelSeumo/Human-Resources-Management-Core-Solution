<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Filters\Common\Auth\RoleFilter as AppRoleFilter;
use App\Filters\Core\RoleFilter;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\Role;
use Illuminate\Database\Eloquent\Builder;

class TenantRoleAPIController extends Controller
{
    public function __construct(RoleFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index()
    {
        return (new AppRoleFilter(
            Role::query()
                ->when(optional(tenant())->id, function (Builder $builder) {
                    $builder->where('tenant_id', optional(tenant())->id);
                }, function (Builder $builder) {
                    $builder->whereNull('tenant_id');
                })->where(fn (Builder $b) => $b
                    ->where('alias', '!=','department_manager')
                    ->orWhereNull('alias'))
        ))->filter()
            ->filters($this->filter)
            ->get(['id', 'name', 'is_default', 'is_admin', 'alias']);
    }

    public function filterRoles()
    {
        return (new AppRoleFilter(
            Role::query()
                ->when(optional(tenant())->id, function (Builder $builder) {
                    $builder->where('tenant_id', optional(tenant())->id);
                }, function (Builder $builder) {
                    $builder->whereNull('tenant_id');
                })
        ))->filter()
            ->filters($this->filter)
            ->get(['id', 'name', 'is_default', 'is_admin', 'alias']);
    }
}
