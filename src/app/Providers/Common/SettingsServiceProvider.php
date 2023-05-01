<?php

namespace App\Providers\Common;

use App\Config\SetMailConfig;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            SetMailConfig::new(true)
                ->clear()
                ->set();
        }catch (\Exception $exception) {}
    }
}
