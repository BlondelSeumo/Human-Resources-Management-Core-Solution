<?php

use App\Helpers\Core\General\SanitizerHelper;

if (!function_exists('sanitize_data')) {
    /**
     * @param $value
     * @return mixed
     */
    function sanitize_data($value): mixed
    {
        return resolve(SanitizerHelper::class)->filterData($value);
    }
}