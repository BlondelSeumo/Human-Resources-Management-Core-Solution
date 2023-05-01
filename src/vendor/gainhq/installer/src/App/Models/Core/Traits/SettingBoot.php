<?php

namespace Gainhq\Installer\App\Models\Core\Traits;

use Gainhq\Installer\App\Hooks\AfterSettingSaved;

trait SettingBoot
{
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            AfterSettingSaved::new()
                ->setModel($setting)
                ->handle();
        });

        static::updated(function ($setting) {
            AfterSettingSaved::new()
                ->setModel($setting)
                ->handle();
        });
    }
}
