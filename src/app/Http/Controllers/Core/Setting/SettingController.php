<?php


namespace App\Http\Controllers\Core\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Setting\SettingRequest;
use App\Notifications\Core\Settings\SettingsNotification;
use App\Services\Core\Setting\SettingService;
use Illuminate\Http\Response;

class SettingController extends Controller
{

    public function __construct(SettingService $setting)
    {
        $this->service = $setting;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->service->getFormattedSettings();
    }


    /**
     * @param SettingRequest $request
     * @return array
     */
    public function store(SettingRequest $request)
    {
        $setting = $this->service
            ->save();

        return created_responses('setting');
    }


    /**
     * @param SettingRequest $request
     * @return array
     */
    public function update(SettingRequest $request)
    {
        $this->service->update();

        notify()
            ->on('settings_updated')
            ->with(trans('default.general_settings'))
            ->send(SettingsNotification::class);

        return updated_responses('settings', [
            'settings' => $this->service->getFormattedSettings()
        ]);
    }


    /**
     * @return array
     */
    public function destroy()
    {
        if ($this->service->delete()) {
            return deleted_responses('setting');
        }
        return failed_responses();
    }
}
