<?php


class Requirement
{
    protected $results = [];
    protected $config;
    protected $phpRequirementMissingCount = 0;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getPhpVersion()
    {
        $version = PHP_VERSION;
        preg_match("#^\d+(\.\d+)*#", $version, $filtered);
        $currentVersion = $filtered[0];

        return [
            'full' => $version,
            'version' => $currentVersion,
        ];
    }

    public function checkPhpVersion(string $minPhpVersion = null)
    {
        $minVersionPhp = $minPhpVersion;
        $currentPhpVersion = $this->getPhpVersion();
        $supported = false;

        if ($minPhpVersion == null) {
            $minVersionPhp = $this->getMinPhpVersion();
        }

        if (version_compare($currentPhpVersion['version'], $minVersionPhp) >= 0) {
            $supported = true;
        }

        return [
            'full' => $currentPhpVersion['full'],
            'current' => $currentPhpVersion['version'],
            'minimum' => $minVersionPhp,
            'supported' => $supported,
        ];
    }

    public function getMinPhpVersion()
    {
        return $this->config->core->min_php_version;
    }

    public function check(array $requirements = [])
    {
        $requirements = count($requirements) ? $requirements : $this->config->requirements;
        $requirements = (array)$requirements;

        foreach ($requirements as $type => $requirement) {
            switch ($type) {
                // check php requirements
                case 'php':
                    $this->checkPhpRequirements($requirements, $type);
                    break;
                // check apache requirements
                case 'apache':
                    $this->checkApacheRequirements($requirements, $type);
                    break;
            }
        }
        $this->results['errors'] = $this->phpRequirementMissingCount > 0;
        return $this->results;
    }

    public function checkPhpRequirements($requirements, $type)
    {
        foreach ($requirements[$type] as $requirement) {
            $this->results['requirements'][$type][$requirement] = true;

            if (!extension_loaded($requirement)) {
                $this->results['requirements'][$type][$requirement] = false;
                $this->phpRequirementMissingCount++;
                $this->results['errors'] = true;
            }
        }
        return $this->results;
    }

    public function checkApacheRequirements($requirements, $type)
    {
        foreach ($requirements[$type] as $requirement) {
            // if function doesn't exist we can't check apache modules
            if (function_exists('apache_get_modules')) {
                $this->results['requirements'][$type][$requirement] = true;

                if (!in_array($requirement, apache_get_modules())) {
                    $this->results['requirements'][$type][$requirement] = false;
                    $this->phpRequirementMissingCount++;
                    $this->results['errors'] = true;
                }
            }
        }
        return $this->results;
    }

    public function isSupported()
    {
        if ($this->checkPhpVersion()['supported']) {
            $requirements = array_filter($this->check()['requirements']['php'], function ($r) {
                return !$r;
            });
            return !count($requirements);
        }
        return false;
    }
}
