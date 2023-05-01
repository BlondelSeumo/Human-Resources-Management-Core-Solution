<?php

namespace Gainhq\Installer\App\Models\Core\Traits;

trait SettingRelationship
{
    public function settingable()
    {
        return $this->morphTo();
    }
}
