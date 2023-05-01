<?php

use App\Helpers\Settings\SettingParser;

if (!function_exists('settings')) {

    function settings(string $key = null, $alternate = null) {
        return SettingParser::new(true)
            ->parse($key, $alternate);
    }

}
