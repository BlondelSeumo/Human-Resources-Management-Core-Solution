<?php


namespace App\Helpers\Traits;


trait MakeArrayFromString
{
    public function makeArray($string, $delimiter = ',')
    {
        $string = explode($delimiter, $string);

        return array_filter($string, fn ($d) => $d);
    }
}