<?php


namespace App\Http\Composer\Helper;


use App\Helpers\Core\Traits\InstanceCreator;

class LeavePermissions
{
    use InstanceCreator;

    public function permissions()
    {
        return [
            [
                'name' => __t('leave_status'),
                'url' => route('support.leave.status',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_leave_status'])
            ],
            [
                'name' => __t('leave_request'),
                'url' => route('support.leave.requests',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_leave_requests'])
            ],
            [
                'name' => __t('calendar'),
                'url' => route('support.leave.calendar',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_leave_calendar'])
            ],
            [
                'name' => __t('summery'),
                'url' => route('support.leave.summaries',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ]),
                'permission' => authorize_any(['view_leave_summaries'])
            ],
        ];
    }

    public function canVisit()
    {
        return authorize_any([
            'view_leave_status',
            'view_leaves',
            'view_leave_calendar',
            'view_leave_summaries',
            'view_leave_periods'
        ]);
    }

    public function requestUrl(): string
    {
        return route('support.leave.requests',
            optional(tenant())->is_single ? '' : [
                'tenant_parameter' => optional(tenant())->short_name ?: 'default-tenant'
            ]
        );
    }
    public function summaryUrl(): string
    {
        return route('support.leave.summaries',
            optional(tenant())->is_single ? '' : [
                'tenant_parameter' => optional(tenant())->short_name ?: 'default-tenant'
            ]
        );
    }

    public function parseNotificationUrl($user_id, $status): string
    {
        $urls = [
            'status_approved' => LeavePermissions::new(true)->summaryUrl()."?from=email",
            'status_assigned' => LeavePermissions::new(true)->summaryUrl()."?from=email",
            'status_bypassed' => LeavePermissions::new(true)->requestUrl()."?from=email",
            'status_pending' => LeavePermissions::new(true)->requestUrl()."?from=email",
            'status_rejected' => LeavePermissions::new(true)->summaryUrl()."?from=email",
            'status_canceled' => LeavePermissions::new(true)->summaryUrl()."?from=email",
        ];

        if (isset($urls[$status])) {
            return $urls[$status];
        }

        return LeavePermissions::new(true)->requestUrl()."?from=email";
    }
}
