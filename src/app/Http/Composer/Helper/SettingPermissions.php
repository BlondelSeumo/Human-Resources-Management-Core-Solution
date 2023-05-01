<?php


namespace App\Http\Composer\Helper;


use App\Helpers\Core\Traits\InstanceCreator;

class SettingPermissions
{
    use InstanceCreator;

    public function permissions()
    {
        return [
            [
                'name' => __t('app_settings'),
                'url' => route('support.tenant.settings', optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any([
                    'view_settings',
                    'view_notification_settings',
                    'check_for_updates',
                    'view_delivery_settings'
                ])
            ],
            [
                'name' => __t('leave_settings'),
                'url' => route('support.settings.leave',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_leave_settings'])
            ],
            [
                'name' => __t('attendance_settings'),
                'url' => route('support.settings.attendance',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_attendance_settings'])
            ],
            [
                'name' => __t('payroll_settings'),
                'url' => route('support.settings.payroll',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_payroll_settings'])
            ],
            [
                'name' => __t('import'),
                'url' => route('support.settings.import',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['import_employees', 'import_attendances'])
            ],
        ];
    }

    public function canVisit()
    {
        return authorize_any([
            'view_settings',
            'view_notification_templates',
            'check_for_updates',
            'view_notification_settings',
            'view_payroll_settings',
            'view_attendance_settings',
            'import_employees',
            'import_attendances',
        ]);
    }
}
