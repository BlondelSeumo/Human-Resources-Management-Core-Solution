<?php

if (! function_exists('include_route_files')) {
    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function include_route_files($folder)
    {
        try {
            $rdi = new RecursiveDirectoryIterator($folder);
            $it = new RecursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (! $it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (!function_exists('__t')) {

    function __t($key = '', $options = [], $isCapitalized = false) {

        $vars = count($options) ? array_merge(...array_map(function ($k) use ($options) {
            $value = __("default.$options[$k]");
            return [
                "{".$k."}" =>  $value,
                "{ $k }" =>  $value,
                "{ $k}" => $value,
                "{".$k." }" =>  $value,
                ":$k" => $value
            ];
        }, array_keys($options))) : [];

        $string = strtr(__("default.{$key}"), $vars);
        return $isCapitalized ? ucwords($string) : $string;
    }
}
