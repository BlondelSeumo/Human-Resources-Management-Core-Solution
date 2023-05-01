<?php

namespace App\Http\Controllers\Tenant\Attendance;

use App\Helpers\Traits\TenantAble;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Attendance\AttendanceSettingsRequest as Request;
use App\Repositories\Core\BaseRepository;
use App\Repositories\Core\Setting\SettingRepository;
use App\Services\Core\Setting\SettingService;

class ModuleSettingController extends Controller
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
            'module', $setting_able_type, $setting_able_id
        );
    }

    public function update(Request $request)
    {
        $request->validate([
            'list' => ['array']
        ]);

        [$setting_able_id, $setting_able_type] = $this->tenantAble();

        $attributes = ['list' => json_encode($request->get('list', []))];

        $this->service->saveSettings(
            $attributes,
            'module',
            $setting_able_type,
            $setting_able_id
        );

        return updated_responses('module_settings');
    }
}
