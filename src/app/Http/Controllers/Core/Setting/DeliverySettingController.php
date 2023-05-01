<?php

namespace App\Http\Controllers\Core\Setting;

use App\Hooks\Settings\AfterDeliverySettingSaved;
use App\Hooks\Settings\BeforeDeliverySettingSaved;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Setting\DeliverySettingRequest as Request;
use App\Notifications\Core\Settings\SettingsNotification;
use App\Services\Core\Setting\DeliverySettingService;

class DeliverySettingController extends Controller
{
    public function __construct(DeliverySettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $default = $this->service->getDefaultSettings();

        return $this->service
            ->getFormattedDeliverySettings([optional($default)->value, 'default_mail_email_name']);
    }

    public function update(Request $request)
    {
        $context = $request->get('provider');

        BeforeDeliverySettingSaved::new()
            ->handle();

        foreach ($request->only('from_name', 'from_email') as $key => $value) {
            $this->service
                ->update($key, $value, 'default_mail_email_name');
        }

        foreach ($request->except('allowed_resource', 'from_name', 'from_email') as $key => $value) {
            $this->service
                ->update($key, $value, $context);
        }

        $this->service->setDefaultSettings('default_mail', $context);

        notify()
            ->on('settings_updated')
            ->with(trans('default.delivery'))
            ->send(SettingsNotification::class);

        AfterDeliverySettingSaved::new()
            ->handle();

        return updated_responses('delivery_settings');
    }

    public function show($provider)
    {
        return $this->service
            ->getFormattedDeliverySettings([$provider, 'default_mail_email_name']);
    }
}
