<?php

namespace Gainhq\Installer\App\Helpers\Traits;

trait InstanceCreator
{
    protected static $instances = [];

    public static function new($singleton = false, ...$arguments)
    {
        if ($singleton) {
            if (isset(static::$instances[get_called_class()])) {
                return static::$instances[get_called_class()];
            }
            return static::$instances[get_called_class()] = new static(...$arguments);
        }
        return new static(...$arguments);
    }
}
