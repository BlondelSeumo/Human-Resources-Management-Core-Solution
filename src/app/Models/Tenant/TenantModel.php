<?php


namespace App\Models\Tenant;


use App\Models\Core\BaseModel;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use App\Models\Tenant\Scopes\TenantScope;

class TenantModel extends BaseModel
{
    use DescriptionGeneratorTrait;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (
                in_array('tenant_id', (new static())->getFillable()) &&
                !app()->runningInConsole() &&
                empty(optional($model)->tenant_id)
            ) {
                $model->fill([
                    'tenant_id' => optional(tenant())->id
                ]);
            }
        });

        static::addGlobalScope(new TenantScope());
    }
}
