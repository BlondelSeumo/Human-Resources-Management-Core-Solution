<?php

namespace App\Models\Tenant\Payroll;

use App\Models\Core\BaseModel;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiary extends TenantModel
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'description', 'is_active'];

    public function values()
    {
        return $this->hasMany(BeneficiaryValue::class);
    }
}
