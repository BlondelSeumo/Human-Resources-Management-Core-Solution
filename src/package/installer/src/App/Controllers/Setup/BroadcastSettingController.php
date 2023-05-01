<?php

namespace Gainhq\Installer\App\Controllers\Setup;

use Gainhq\Installer\App\Controllers\Controller;
use Gainhq\Installer\App\Hooks\InstallationSaved;
use Gainhq\Installer\App\Request\BroadcastRequest;
use Gainhq\Installer\App\Services\DeliverySettingService;

class BroadcastSettingController extends Controller
{
    public function __construct(DeliverySettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $default = $this->service->getDefaultSettings($key = 'default_broadcast');

        return $this->service
            ->getFormattedDeliverySettings([optional($default)->value, 'default_broadcast_driver_name']);
    }

    public function update(BroadcastRequest $request)
    {
        $context = $request->get('broadcast_driver');

        foreach ($request->only('pusher_app_id', 'pusher_app_key', 'pusher_app_secret', 'pusher_app_cluster') as $key => $value) {
            $this->service
                ->update($key, $value, 'default_broadcast_driver_name');
        }

        foreach ($request->except('allowed_resource', 'pusher_app_id', 'pusher_app_key', 'pusher_app_secret', 'pusher_app_cluster') as $key => $value) {
            $this->service
                ->update($key, $value, $context);
        }

        $this->service->setDefaultSettings('default_broadcast', $context, $context = 'broadcast');

        InstallationSaved::new()
            ->setEnvAndFinishInstallation();

        return updated_responses('broadcast_settings');
    }
}
