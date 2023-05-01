<?php


namespace App\Hooks\User;


use App\Helpers\Core\Traits\InstanceCreator;
use App\Hooks\HookContract;
use App\Models\Core\Setting\Setting;

class AfterSettingSaved extends HookContract
{
    use InstanceCreator;

    public function handle()
    {
        /**@var $settings Setting*/
        $settings = $this->model;

        if ($settings->context === 'app') {
            cache()->forget('app-settings-global');
        }

        cache()->forget('app-delivery-settings');
        cache()->forget('punch-in-out-alert');

        cache()->forget($settings->context.'_settings_cached');

        if (optional($this->model)->context == 'tenant') {
            cache()->forget('app-current-tenant-setting-' . optional($this->model)->settingable_id);
        }

        cache()->forget('app-tenant-module-setting-' . optional($this->model)->settingable_id);

        if ($settings->name == 'language') {
            session()->forget('locale');
        }
    }
}
