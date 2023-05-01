<?php


namespace App\Http\Controllers\Tenant\Settings;


use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\TenantSettingRequest as Request;
use App\Notifications\Core\Settings\SettingsNotification;
use App\Services\Tenant\Setting\SettingService;
use Illuminate\Support\Facades\Artisan;

class GeneralSettingController extends Controller
{
    public function __construct(SettingService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->getFormattedTenantSettings();
    }

    public function update(Request $request)
    {
        $this->service->update();

        notify()
            ->on('settings_updated')
            ->with(trans('default.general_settings'))
            ->send(SettingsNotification::class);

        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('queue:restart');

        return updated_responses('settings');
    }
}
