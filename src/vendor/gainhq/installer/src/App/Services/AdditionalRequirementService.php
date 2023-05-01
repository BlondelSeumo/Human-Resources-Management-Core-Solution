<?php


namespace Gainhq\Installer\App\Services;

use Gainhq\Installer\App\Managers\StorageManager;


class AdditionalRequirementService extends BaseService
{
    public function checkSymlinkPermission()
    {
        $response = resolve(StorageManager::class)->link();
        return [
            'symlink' => [
                'requirement' => 'Symlink',
                'documentation_link' => config('installer.link.symlink_requirement'),
                'message' => $response['message'],
                'status' => $response['status'],
                'optional' => $response['optional']
            ]
        ];
    }
}