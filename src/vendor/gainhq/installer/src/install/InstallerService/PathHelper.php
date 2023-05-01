<?php

class PathHelper
{
    static protected $self;

    protected $base_path = '';

    public function __construct()
    {
        $current_directory = __DIR__;

        $explode = explode(DIRECTORY_SEPARATOR, $current_directory);


        if (in_array('public', $explode)) {

            $this->base_path = str_replace(
                DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'install',
                '',
                $current_directory
            );
        }else {
            $this->base_path = str_replace(
                'install',
                'src',
                $current_directory
            );
        }
    }

    public function getBasePath()
    {
        return $this->base_path.DIRECTORY_SEPARATOR;
    }

    public function publicPath()
    {
        return $this->getBasePath()."public".DIRECTORY_SEPARATOR;
    }

    public function getStoragePath()
    {
        return $this->getBasePath()."storage".DIRECTORY_SEPARATOR;
    }

    public function getConfigPath()
    {
        return $this->getBasepath()."config".DIRECTORY_SEPARATOR;
    }

    public static function new()
    {
        if (gettype(self::$self) != 'object') {
            self::$self = (new self());
        }
        return self::$self;
    }

    public function permissionBasePath()
    {
       return  str_replace(
            'InstallerService/',
            '',
            $this->getBasepath()
        );
    }

}
