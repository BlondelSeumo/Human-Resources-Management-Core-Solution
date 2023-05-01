<?php

namespace App\Providers;

use App\Http\Composer\TenantDashboardComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Class ComposerServiceProvider.
 */
class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function boot()
    {
        View::composer('layout.tenant', TenantDashboardComposer::class);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        //
    }
}
