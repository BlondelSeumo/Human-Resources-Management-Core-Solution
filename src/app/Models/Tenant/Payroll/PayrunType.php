<?php

namespace App\Models\Tenant\Payroll;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PayrunType extends TenantModel
{
    use HasFactory;

    protected $fillable = ['name', 'is_default', 'eligible_audience'];

    public function setting()
    {
        return $this->morphOne(PayrunSetting::class, 'payrun_settingable');
    }

    public function beneficiaries()
    {
        return $this->morphMany(BeneficiaryValue::class, 'beneficiary_valuable');
    }

    public static function getDefault()
    {
        return self::where('is_default', true)->with('setting')->first();
    }
}
