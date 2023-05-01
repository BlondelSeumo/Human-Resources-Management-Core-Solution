<?php

namespace App\Http\Controllers\Tenant\Settings;

use App\Http\Controllers\Controller;
use App\Services\Settings\SettingService;

class TenantDeliveryController extends Controller
{
    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }

    public function isExists()
    {
        return count($this->service->getCachedMailSettings()) ? 1 : 0;
    }

}
