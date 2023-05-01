<?php

namespace Gainhq\Installer\App\Managers;

use Gainhq\Installer\App\Repositories\Setting\SettingRepository;

class PurchaseCodeManager
{
    public function store($code)
    {
        $settings = resolve(SettingRepository::class)
            ->createSettingInstance('purchase_code', 'purchase_code');

        $settings->fill([
            'name' => 'purchase_code',
            'context' => 'purchase_code',
            'value' => $code
        ])->save();

        return $settings;
    }

    public function getCode()
    {
        $settings = resolve(SettingRepository::class)
            ->findAppSettingWithName('purchase_code', 'purchase_code');

        return $settings ? $settings->value : null;
    }
}
