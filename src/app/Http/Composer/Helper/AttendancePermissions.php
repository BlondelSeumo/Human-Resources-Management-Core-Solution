<?php


namespace App\Http\Composer\Helper;


use App\Helpers\Core\Traits\InstanceCreator;

class AttendancePermissions
{
    use InstanceCreator;

    public function permissions()
    {
        return [
            [
                'name' => __t('daily_log'),
                'url' => $this->dailyLogUrl(),
                'permission' => authorize_any(['attendances_daily_log'])
            ],
            [
                'name' => __t('attendance_request'),
                'url' => $this->requestUrl(),
                'permission' => authorize_any(['view_attendance_requests'])
            ],
            [
                'name' => __t('attendance_details'),
                'url' => $this->detailsUrl(),
                'permission' => authorize_any(['view_attendances_details'])
            ],
            [
                'name' => __t('summery'),
                'url' => $this->summaryUrl(),
                'permission' => authorize_any(['view_attendance_summary'])
            ]
        ];
    }

    public function requestUrl()
    {
        return route('support.attendances.requests',
            optional(tenant())->is_single ? '' : [
                'tenant_parameter' => optional(tenant())->short_name ?: 'default-tenant'
            ]
        );
    }

    public function detailsUrl()
    {
        return route('support.attendances.details',
            optional(tenant())->is_single ? '' : [
                'tenant_parameter' => optional(tenant())->short_name ?: 'default-tenant'
            ]
        );
    }

    public function summaryUrl()
    {
        return route('support.attendances.summaries',
            optional(tenant())->is_single ? '' : [
                'tenant_parameter' => optional(tenant())->short_name ?: 'default-tenant'
            ]
        );
    }

    public function dailyLogUrl()
    {
        return route('support.attendances.lists',
            optional(tenant())->is_single ? '' : [
                'tenant_parameter' => optional(tenant())->short_name ?: 'default-tenant'
            ]
        );
    }

    public function canVisit()
    {
        return authorize_any(['view_attendances_details', 'attendances_daily_log', 'create_attendances', 'attendance_requests']);
    }

    public function parseNotificationUrl($user_id, $status)
    {
        $urls = [
            'status_approve' => AttendancePermissions::new(true)->summaryUrl()."?from=email",
            'status_pending' => AttendancePermissions::new(true)->requestUrl()."?from=email",
            'status_reject' => AttendancePermissions::new(true)->summaryUrl()."?from=email",
            'status_cancel' => AttendancePermissions::new(true)->summaryUrl()."?from=email",
        ];

        if (isset($urls[$status])) {
            return $urls[$status];
        }

        return AttendancePermissions::new(true)->requestUrl()."?from=email";
    }
}
