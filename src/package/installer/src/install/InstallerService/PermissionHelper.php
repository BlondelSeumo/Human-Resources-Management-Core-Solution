<?php

include 'PathHelper.php';

class PermissionHelper
{
    protected $results = [];

    protected $config = [];

    protected $path;

    public function __construct($config = [])
    {
        $this->results['permissions'] = [];

        $this->results['errors'] = null;

        $this->config = $config;

        $this->path = PathHelper::new();
    }

    public function check(array $permissions = [])
    {

        $permissions = count($permissions) ? $permissions : $this->config->permissions;
        $permissions = (array)$permissions;

        foreach ($permissions as $folder => $permission) {
            !($this->getPermission($folder, $permission)) ? $this->setResponseWithSetErrors($folder, $permission, false)
                : $this->setResponse($folder, $permission, true);
        }

        return $this->results;
    }

    public function getPermission($folder, $permission)
    {
        $permission_base_path = $this->path->permissionBasePath();

        if (is_dir($permission_base_path . $folder)) {
            return $this->directoryHasPermission($folder, $permission);
        } else {
            if ($folder === '.env' || $folder === 'config/gain.php' || $folder === '.htaccess') {
                return $this->directoryHasPermission($folder, $permission);
            }
            return false;
        }
    }

    private function setResponseWithSetErrors($folder, $permission, $isSet)
    {
        $this->setResponse($folder, $permission, $isSet);
        $this->results['errors'] = true;
    }

    private function setResponse($folder, $permission, $isSet)
    {
        array_push($this->results['permissions'], [
            'folder' => $folder,
            'permission' => $permission,
            'isSet' => $isSet,
        ]);
    }

    public function isSupported()
    {
        $this->check();
        return !$this->results['errors'];
    }

    private function directoryHasPermission($directory, $permission): bool
    {
        $permission_base_path = $this->path->permissionBasePath();
        /*check htaccess file exists
         * if ($directory === '.htaccess') {
            $location = file_exists($permission_base_path . 'public/' ) ?
                $permission_base_path . 'public/' . $directory : $permission_base_path. $directory ;
        }else{
            $location = $permission_base_path . $directory;
        }*/

        $location = $permission_base_path . $directory;
        $directoryPermission = ltrim(substr(sprintf('%o', fileperms($location)), -4), '0');
        return intval($directoryPermission) >= intval($permission);
    }
}
