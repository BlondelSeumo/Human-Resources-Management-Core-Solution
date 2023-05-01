<?php


namespace App\Models\Core\Setting\Traits;


use App\Hooks\User\AfterSettingSaved;

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
