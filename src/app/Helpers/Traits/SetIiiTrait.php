<?php


namespace App\Helpers\Traits;


trait SetIiiTrait
{
    public function setMemoryLimit($size = '256M')
    {
        ini_set('memory_limit', $size);
    }

    public function setExecutionTime($time = 420)
    {
        set_time_limit($time);
    }
}
