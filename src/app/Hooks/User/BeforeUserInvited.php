<?php


namespace App\Hooks\User;


use App\Exceptions\GeneralException;
use App\Hooks\HookContract;
use App\Models\Core\Auth\Role;

class BeforeUserInvited extends HookContract
{

    public function handle()
    {
        if (optional(tenant())->is_single) {
            return true;
        }

        if (!optional(tenant())->id) {
            return true;
        }

        $tenant_roles = Role::query()->where('tenant_id', tenant()->id)->get();
        foreach (request('roles', []) as $role) {
            if (!$tenant_roles->contains($role)) {
                throw new GeneralException(__t('action_not_allowed'), 403);
            }
        }
    }

}
