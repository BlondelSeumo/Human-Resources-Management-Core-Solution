<?php

namespace App\Models\Tenant\Assets;

use App\Models\Tenant\TenantModel;

class CompanyAssetType extends TenantModel
{
    protected $fillable = [
        'name',
    ];
}
