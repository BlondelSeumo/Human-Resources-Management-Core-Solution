<?php

namespace App\Models\Tenant\Assets;

use App\Models\Core\Auth\User;
use App\Models\Core\Traits\BootTrait;
use App\Models\Core\Traits\CreatedByRelationship;
use App\Models\Tenant\TenantModel;

class CompanyAsset extends TenantModel
{
    use BootTrait, CreatedByRelationship;
    protected $fillable = [
        'created_by',
        'user_id',
        'name',
        'code',
        'serial_number',
        'type_id',
        'is_working',
        'date',
        'note',
    ];

    public function type()
    {
        return $this->belongsTo(CompanyAssetType::class, 'type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
