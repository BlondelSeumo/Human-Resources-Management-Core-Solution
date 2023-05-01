<?php

namespace App\Http\Controllers\Core\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Setting\CornJobRequest;
use App\Services\Core\Setting\SettingService;

class CornJobSettingController extends Controller
{
    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service
            ->getFormattedSettings(config('settings.corn-job-context'));
    }

    public function update(CornJobRequest $request)
    {
        $settings = $this->service->updateCornJobSetting();
        return updated_responses('corn_job', [
            'settings' => $settings
        ]);
    }

}
