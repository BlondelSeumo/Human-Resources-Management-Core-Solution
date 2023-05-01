<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class HelperServiceProvider.
 */
class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function boot()
    {
        $this->registerHelper(app_path('Helpers'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR.'Global'));
        $this->registerHelper(app_path('Helpers'.DIRECTORY_SEPARATOR.'Tenant'));
    }

    public function registerHelper($folder)
    {
        $rdi = new RecursiveDirectoryIterator($folder);
        $it = new RecursiveIteratorIterator($rdi);

        while ($it->valid()) {
            if (
                ! $it->isDot() &&
                $it->isFile() &&
                $it->isReadable() &&
                $it->current()->getExtension() === 'php' &&
                strpos($it->current()->getFilename(), 'Helper')
            ) {
                require $it->key();
            }

            $it->next();
        }
    }
}
