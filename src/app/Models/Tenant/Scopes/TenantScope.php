<?php


namespace App\Models\Tenant\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{

    public function apply(Builder $builder, Model $model)
    {
        if (app()->runningInConsole()){
            return;
        }

        if (app()->runningUnitTests()) {
            return;
        }

        $condition = in_array('tenant_id', $model->getFillable()) && optional(tenant())->id;

        $builder->when($condition, function (Builder  $builder) {
            $builder->where('tenant_id', tenant()->id);
        });
    }
}
