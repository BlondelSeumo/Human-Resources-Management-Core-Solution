<?php


namespace App\Models\Core\Traits\Translate;


trait TranslatedNameTrait
{
    public function getTranslatedNameAttribute()
    {
        return trans("default.{$this->attributes['name']}");
    }

}
