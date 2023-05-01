<?php


namespace Gainhq\Installer\App\Managers\Helper;

class PermissionsHelper
{
    protected $results = [];

    public function __construct()
    {
        $this->results['permissions'] = [];

        $this->results['errors'] = null;
    }


    public function check(array $permissions = [])
    {
        $permissions = count($permissions) ? $permissions: config('install.permissions');
        foreach ($permissions as $folder => $permission) {
            if (! ($this->getPermission($folder) >= $permission)) {
                $this->addFileAndSetErrors($folder, $permission, false);
            } else {
                $this->addFile($folder, $permission, true);
            }
        }

        return $this->results;
    }

    public function createTestFile($folder)
    {
        try {
            $file = fopen(base_path($folder.'test.txt'), 'w');
            fwrite($file, "John Doe\n");
            fclose($file);
            $this->deleteTestFile(base_path($folder.'test.txt'));
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function deleteTestFile($file_path)
    {
        unlink($file_path);
    }

    public function testFile($file_path)
    {
        try {
            $fp = fopen(base_path($file_path), 'a');
            fwrite($fp, 'TEST=TEST');
            fclose($fp);
            file_put_contents(base_path($file_path), str_replace(
                'TEST=TEST', '', file_get_contents(base_path($file_path))
            ));
            return true;
        }catch (\Exception $exception) {
            return false;
        }
    }

    private function getPermission($folder)
    {
        if (is_dir(base_path($folder))) {
            return $this->createTestFile($folder);
        }else {
            return $this->testFile($folder);
        }
    }



    private function addFile($folder, $permission, $isSet)
    {
        array_push($this->results['permissions'], [
            'folder' => $folder,
            'permission' => $permission,
            'isSet' => $isSet,
        ]);
    }

    private function addFileAndSetErrors($folder, $permission, $isSet)
    {
        $this->addFile($folder, $permission, $isSet);

        $this->results['errors'] = true;
    }

    public function isSupported()
    {
        $this->check();

        return !$this->results['errors'];
    }
}
