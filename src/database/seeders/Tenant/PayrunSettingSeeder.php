<?php

namespace Database\Seeders\Tenant;

use App\Models\Tenant\Payroll\Beneficiary;
use App\Models\Tenant\Payroll\PayrunSetting;
use App\Models\Tenant\Payroll\PayrunType;
use Illuminate\Database\Seeder;

class PayrunSettingSeeder extends Seeder
{

    public function run()
    {
        $payrun_type = PayrunType::query()->create([
            'name' => 'Default Payrun',
            'is_default' => 1,
            'tenant_id' => 1,
        ]);
        PayrunSetting::query()->create([
            'payrun_settingable_type' => PayrunType::class,
            'payrun_settingable_id' => $payrun_type->id,
            'payrun_period' => 'monthly',
            'consider_type' => 'none',
            'consider_overtime' => 0
        ]);
        Beneficiary::query()->insert([
            [
                'name' => 'Bonus',
                'type' => 'allowance',
                'is_active' => 1,
            ],
            [
                'name' => 'Tax',
                'type' => 'deduction',
                'is_active' => 1,
            ],
        ]);
        $payrun_type->beneficiaries()->create([
            'amount' => 10,
            'beneficiary_id' => 1,
            'is_percentage' => 1,
        ]);
        $payrun_type->beneficiaries()->create([
            'amount' => 2,
            'beneficiary_id' => 2,
            'is_percentage' => 1,
        ]);
    }
}
