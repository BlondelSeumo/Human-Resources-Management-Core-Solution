<?php


namespace App\Helpers\Core\General;

class SanitizerHelper
{
    /**
     * @param $value
     * @return mixed
     */
    public function filterData($value)
    {
        return filter_var($value, $this->generateSanitizer($value ?: ''));
    }

    /**
     * @param $data
     * @return false|int
     */
    public function generateSanitizer($data)
    {
        $type = gettype($data);
        return filter_id($type == 'integer' ? 'int' : $type);
    }
}
