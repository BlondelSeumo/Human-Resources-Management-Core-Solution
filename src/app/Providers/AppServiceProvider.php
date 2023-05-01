<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (!$this->app->environment('production') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        }
    }


    public function boot()
    {
        setlocale(LC_TIME, config('app.locale_php'));

        Carbon::setLocale(config('app.locale'));


        if (! app()->runningInConsole()) {
            if (config('locale.languages')[config('app.locale')][2]) {
                session(['lang-rtl' => true]);
            } else {
                session()->forget('lang-rtl');
            }
        }
    }
}
