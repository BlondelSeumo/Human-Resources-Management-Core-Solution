<?php


namespace App\Http\Composer;


use App\Helpers\Settings\SettingParser;
use App\Http\Composer\Helper\AdministrationPermissions;
use App\Http\Composer\Helper\AttendancePermissions;
use App\Http\Composer\Helper\EmployeePermissions;
use App\Http\Composer\Helper\LeavePermissions;
use App\Http\Composer\Helper\LogoIcon;
use App\Http\Composer\Helper\PayrollPermissions;
use App\Http\Composer\Helper\SettingPermissions;
use App\Models\Tenant\WorkingShift\WorkingShift;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Settings\SettingService;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class TenantDashboardComposer
{
    public function compose(View $view)
    {
        ['logo' => $logo, 'icon' => $icon] = LogoIcon::new(true)
            ->logoIcon();

        if (!Cache::get('has_default_work_shift')) {
            Cache::forget('has_default_work_shift');

            Cache::rememberForever('has_default_work_shift', function () {
                return WorkingShift::query()
                    ->where('is_default', 1)
                    ->exists();
            });
        }

        $module_list = resolve(SettingService::class)->getCachedTenantModuleSettings()['list'] ?? [];

        $view->with([
            'permissions' => [
                [
                    'name' => __t('dashboard'),
                    'url' => route('tenant.dashboard', optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name]),
                    'icon' => 'pie-chart',
                    'permission' => auth()->user()->can('manage_dashboard')
                ],
                [
                    'name' => __t('job_desk'),
                    'icon' => 'user',
                    'url' => EmployeePermissions::new(true)->profile(),
                    'permission' => in_array('job_desk', $module_list),

                ],
                [
                    'name' => __t('employee'),
                    'icon' => 'users',
                    'id' => 'employee',
                    'permission' => in_array('employee', $module_list) && EmployeePermissions::new(true)->canVisit(),
                    'subMenu' => EmployeePermissions::new(true)->permissions(),
                ],
                [
                    'name' => __t('leave'),
                    'icon' => 'clock',
                    'id' => 'leave',
                    'permission' => in_array('leave', $module_list) && LeavePermissions::new(true)->canVisit(),
                    'subMenu' => LeavePermissions::new(true)->permissions(),
                ],
                [
                    'name' => __t('attendance'),
                    'icon' => 'calendar',
                    'id' => 'attendance',
                    'permission' => in_array('attendance', $module_list) && AttendancePermissions::new(true)->canVisit(),
                    'subMenu' => AttendancePermissions::new(true)->permissions(),
                ],
                [
                    'name' => __t('payroll'),
                    'icon' => 'credit-card',
                    'id' => 'payroll',
                    'permission' => in_array('payroll', $module_list) && PayrollPermissions::new(true)->canVisit(),
                    'subMenu' => PayrollPermissions::new(true)->permissions(),
                ],
                AdministrationPermissions::new(true)->canVisit() ?
                    [
                        'name' => __t('administration'),
                        'icon' => 'briefcase',
                        'id' => 'administration',
                        'permission' => in_array('administration', $module_list),
                        'subMenu' => AdministrationPermissions::new(true)->permissions()
                    ] :
                    [
                        'name' => __t('holiday'),
                        'icon' => 'calendar',
                        'url' => AdministrationPermissions::new(true)->holidayUrl(),
                        'permission' => in_array('administration', $module_list) && authorize_any(['view_holidays'])
                    ],
                [
                    'name' => __t('assets'),
                    'icon' => 'plus-square',
                    'url' => route('support.employee.company_assets', optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name]),
                    'permission' => in_array('asset', $module_list) && authorize_any(['view_company_assets', 'create_company_assets'])
                ],
                [
                    'name' => __t('settings'),
                    'id' => 'settings',
                    'icon' => 'settings',
                    'subMenu' => SettingPermissions::new(true)->permissions(),
                    'permission' => SettingPermissions::new(true)->canVisit()
                ],


            ],
            'settings' => SettingParser::new(true)->getSettings(),
            'top_bar_menu' => [
                [
                    'optionName' => __t('my_profile'),
                    'optionIcon' => 'user',
                    'url' => route('tenant.user.profile', optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name])
                ],
                [
                    'optionName' => __t('notifications'),
                    'optionIcon' => 'bell',
                    'url' => route("support.tenant.notifications", optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name])
                ],
                auth()->user()->can('view_settings') ?
                    [
                        'optionName' => __t('settings'),
                        'optionIcon' => 'settings',
                        'url' => authorize_any([
                            'view_settings',
                            'view_corn_job_settings',
                            'view_delivery_settings',
                            'view_notification_settings'
                        ]) ? route("support.tenant.settings", optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name]) : '#'
                    ] : [],
                [
                    'optionName' => __t('log_out'),
                    'optionIcon' => 'log-out',
                    'url' => request()->root() . '/admin/log-out'
                ],
            ],
            'logo' => $logo,
            'logo_icon' => $icon,
            'hasDefaultWorkShift' => Cache::get('has_default_work_shift')
        ]);
    }
}
