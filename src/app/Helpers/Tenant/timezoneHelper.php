<?php


use Illuminate\Support\Carbon;

if (!function_exists('nowFromApp')) {

    function nowFromApp(): Carbon
    {
        return now('UTC');
    }

}

if (!function_exists('todayFromApp')) {

    function todayFromApp(): Carbon
    {
        return today('UTC');
    }

}