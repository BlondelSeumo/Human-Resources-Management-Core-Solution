<?php

namespace App\Http\Controllers\Core\Setting;

use App\Filters\Common\Notification\NotificationEventFilter;
use App\Filters\Common\Notification\NotificationSettingsVerifier;
use App\Filters\Core\NotificationSettingFilter;
use App\Hooks\Settings\AfterNotificationSettingSaved;
use App\Hooks\Settings\BeforeNotificationSettingSaved;
use App\Http\Controllers\Controller;
use App\Models\Core\Setting\NotificationSetting;
use App\Notifications\Core\Settings\SettingsNotification;
use App\Services\Core\Setting\NotificationSettingService;
use App\Http\Requests\Core\Setting\NotificationSettingRequest as Request;

class NotificationSettingController extends Controller
{

    public function __construct(NotificationSettingService $service, NotificationSettingFilter $filter)
    {
        $this->filter = $filter;
        $this->service = $service;
    }

    public function show(NotificationSetting $notificationSetting)
    {
        return $notificationSetting->load('audiences');
    }

    public function update(NotificationSetting $notificationSetting, Request $request)
    {
        BeforeNotificationSettingSaved::new(true)
            ->setModel($notificationSetting)
            ->handle();

        $this->service
            ->update(
                NotificationSettingsVerifier::new()
                    ->verify($notificationSetting)
            );

        if ($audiences = $request->get('audiences', []))
            $this->service->syncAudiences($audiences);

        notify()
            ->on('settings_updated')
            ->with(trans('default.notification'))
            ->send(SettingsNotification::class);

        AfterNotificationSettingSaved::new(true)
            ->setModel($notificationSetting)
            ->handle();

        return updated_responses('notification_settings');
    }
}
