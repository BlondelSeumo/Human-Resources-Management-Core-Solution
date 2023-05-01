<?php

namespace Gainhq\Installer\App\Helpers\Traits;

trait Helpers
{
    /**
     * @param array $data
     * @return array
     */
    public function checkMakeArray($data = null)
    {
        if(is_string($data)) $data = preg_split('/[,|]/', $data);
        return is_array($data)
            ? $data
            : [ $data ];
    }
}
