<?php


namespace App\Models\Tenant\Traits;


trait HasAlias
{
    public static function getByAlias($alias)
    {
        return self::where('alias', $alias)->first();
    }
}