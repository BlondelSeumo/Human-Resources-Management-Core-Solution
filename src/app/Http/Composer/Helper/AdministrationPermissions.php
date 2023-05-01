<?php


namespace App\Http\Composer\Helper;


use App\Helpers\Core\Traits\InstanceCreator;

class AdministrationPermissions
{
    use InstanceCreator;

    public function permissions()
    {
        return [
            [
                'name' => __t('users_roles'),
                'url' => $this->userUrl(),
                'permission' => authorize_any(['view_roles'])
            ],
            [
                'name' => __t('work_shifts'),
                'url' => $this->workShiftUrl(),
                'permission' => authorize_any(['view_working_shifts'])
            ],
            [
                'name' => __t('departments'),
                'url' => $this->departmentUrl(),
                'permission' => authorize_any(['view_departments'])
            ],
            [
                'name' => __t('holiday'),
                'url' => $this->holidayUrl(),
                'permission' => authorize_any(['view_holidays'])
            ],
            [
                'name' => __t('org_structure'),
                'url' => $this->organizationUrl(),
                'permission' => auth()->user()->can('view_departments') &&
                    auth()->user()->can('update_departments')
            ],
            [
                'name' => __t('announcements'),
                'url' => $this->announcementUrl(),
                'permission' => auth()->user()->can('view_announcements')
            ],
        ];
    }

    public function canVisit()
    {
        return authorize_any(['view_users', 'view_roles', 'view_departments', 'view_working_shifts', 'view_announcements']);
    }

    public function departmentUrl()
    {
        return route(
            'support.employee.departments',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function organizationUrl()
    {
        return route(
            'support.organization.structure',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function workShiftUrl()
    {
        return route(
            'support.employee.work_shifts',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function userUrl()
    {
        return route(
            'support.tenant.users',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function holidayUrl()
    {
        return route(
            'support.employee.holidays',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }

    public function announcementUrl()
    {
        return route(
            'support.employee.announcements',
            optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]
        );
    }
}
