<?php

namespace App\Models\Tenant\Payroll;

use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrunSetting extends TenantModel
{
    use HasFactory;

    protected $fillable = ['payrun_period', 'consider_type', 'consider_overtime', 'payrun_settingable_type', 'payrun_settingable_id'];

    public function payrunSettingable()
    {
        return $this->morphTo();
    }

    public function beneficiaries()
    {
        return $this->morphMany(BeneficiaryValue::class, 'beneficiary_valuable');
    }
}
