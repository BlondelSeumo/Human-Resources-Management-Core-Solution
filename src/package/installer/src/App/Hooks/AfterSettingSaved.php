<?php

namespace Gainhq\Installer\App\Hooks;

use Gainhq\Installer\App\Helpers\Traits\InstanceCreator;

class AfterSettingSaved extends HookContract
{

    use InstanceCreator;

    public function handle()
    {
        if ($this->model->context === 'app') {
            cache()->forget('app-settings-global');
        }

        cache()->forget('app-delivery-settings');

        cache()->forget($this->model->context.'_settings_cached');
    }
}