<?php

namespace Gainhq\Installer\App\Models\Core\Traits;

trait TranslatedNameTrait
{
    public function getTranslatedNameAttribute()
    {
        return trans("default.{$this->attributes['name']}");
    }

}
