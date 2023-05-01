<?php


namespace Gainhq\Installer\App\Managers\Helper;


trait SetIiiTrait
{
    public function setMemoryLimit($size = '256M')
    {
        ini_set('memory_limit', $size);
    }

    public function setExecutionTime($time = 300)
    {
        set_time_limit($time);
    }
}