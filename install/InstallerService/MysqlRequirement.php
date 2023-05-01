<?php
include_once 'PhpInfoHelper.php';

class MysqlRequirement
{

    protected $results = [];
    protected $config = [];

    public function __construct($config = [])
    {
        $this->results['mysql_version'] = [];
        $this->results['errors'] = null;
        $this->config = $config;
    }

    public function isMySqlSupported()
    {
        $php_info = phpInfoArray();

        $pdo_mysql = $php_info['pdo_mysql']['client_api_version'];
        $current_mysql = $this->getVersion($pdo_mysql);

        $minimum_mysql = $this->config->core->min_mysql_version;
        $is_supported = floatval($current_mysql) >= floatval($minimum_mysql);
        $this->results['errors'] = !$is_supported;
        $this->results['mysql_version'] = [
            'current' => $current_mysql,
            'minimum' => $minimum_mysql,
            'is_supported' => $is_supported
        ];
        return $this->results;
    }

    public function getVersion($item)
    {
        preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $item, $version);
        return $version[0];
    }
}
