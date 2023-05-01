<?php


namespace App\Helpers\Traits;


trait TenantAble
{
    public function tenantAble()
    {
        return [
            tenant()->id,
            optional(tenant())->is_single ? null : get_class(tenant())
        ];
    }
}
