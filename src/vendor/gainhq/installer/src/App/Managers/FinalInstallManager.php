<?php

namespace Gainhq\Installer\App\Managers;

use Illuminate\Support\Facades\Artisan;

class FinalInstallManager
{
    public function generateKey()
    {
        if (config('installer.final.key')) {
            Artisan::call('key:generate', ['--force' => true]);
        }
        return true;
    }

    public function publishVendorAssets()
    {
        if (config('installer.final.publish')) {
            Artisan::call('vendor:publish', ['--all' => true]);
        }
        return true;
    }

    public function finish()
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    }

    public function clear()
    {
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
    }

    public function finishUpdate()
    {
        Artisan::call('config:cache');
        //Artisan::call('route:cache');
        Artisan::call('view:cache');
        Artisan::call('queue:restart');
    }
}
