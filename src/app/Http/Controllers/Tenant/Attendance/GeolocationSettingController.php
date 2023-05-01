<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Helpers\Traits\TenantAble;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Attendance\AttendanceSettingsRequest as Request;
use App\Repositories\Core\BaseRepository;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Setting\SettingService;

class GeolocationSettingController extends Controller
{
    use TenantAble;

    protected BaseRepository $repository;

    public function __construct(SettingService $service, SettingRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function index()
    {
        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        return $this->repository->getFormattedSettings(
            'geolocation', $setting_able_type, $setting_able_id
        );
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => ['required_if:service_name,mapbox,ip_geolocation']
        ]);

        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        $attributes = $request->only('api_key', 'service_name');

        $this->service->saveSettings(
            $attributes,
            'geolocation',
            $setting_able_type,
            $setting_able_id
        );

        return updated_responses('geolocation_settings');
    }
}
