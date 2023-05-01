<?php


namespace App\Models\Core\File\Traits;


use App\Helpers\Core\Traits\FileHandler;

trait FileAttribute
{
    use FileHandler;

    public function getFullUrlAttribute()
    {
        if (in_array(config('filesystems.default'), ['local', 'public'])) {
            return request()->root().$this->path;
        }
        return $this->path;
    }
}
