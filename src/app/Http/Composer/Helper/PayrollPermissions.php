<?php


namespace App\Http\Composer\Helper;


use App\Helpers\Core\Traits\InstanceCreator;

class PayrollPermissions
{
    use InstanceCreator;

    public function permissions()
    {
        return [
            [
                'name' => __t('payrun'),
                'url' => route('support.payroll.payrun',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_payruns'])
            ],
            [
                'name' => __t('payslip'),
                'url' => route('support.payroll.payslip',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_payslips'])
            ],
            [
                'name' => __t('summery'),
                'url' => route('support.payroll.summery',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_payroll_summery'])
            ],
            [
                'name' => __t('beneficiary_badge'),
                'url' => route('support.payroll.beneficiary-badges',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_beneficiaries'])
            ],
        ];
    }

    public function canVisit()
    {
        return authorize_any([
            'view_beneficiaries',
            'view_payroll_summery',
            'view_payslips',
            'view_payruns',
        ]);
    }
}