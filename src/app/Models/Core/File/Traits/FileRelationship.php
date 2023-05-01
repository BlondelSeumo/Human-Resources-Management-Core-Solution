<?php


namespace App\Models\Core\File\Traits;


trait FileRelationship
{
    public function fileable()
    {
        return $this->morphTo();
    }
}
