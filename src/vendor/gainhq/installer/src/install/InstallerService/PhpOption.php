<?php


class PhpOption
{

    protected $results = [];
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->results['php_options'] = [];
        $this->results['errors'] = null;
    }

    public function checkPhpOptions(array $options = [])
    {
        $phpOptions = count($options) ? $options : $this->config->php_options;

        $errorExists = false;
        
        foreach ($phpOptions as $option => $value){

            $isSet = ini_get($option) ? true : false;
            array_push($this->results['php_options'], [
                'option' => $option,
                'isSet' => $isSet,
            ]);

            if(!$isSet){
                $errorExists = true;
            }
        }

        $this->results['errors'] = $errorExists;

        return $this->results;
    }

}