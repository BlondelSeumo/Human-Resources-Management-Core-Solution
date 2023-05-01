<?php

namespace Gainhq\Installer\App\Controllers\Setup;

use Gainhq\Installer\App\Controllers\Controller;
use Gainhq\Installer\App\Hooks\AfterDeliverySettingSaved;
use Gainhq\Installer\App\Hooks\BeforeDeliverySettingSaved;
use Gainhq\Installer\App\Request\DeliverySettingRequest;
use Gainhq\Installer\App\Services\DeliverySettingService;

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

    public function update(DeliverySettingRequest $request)
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
