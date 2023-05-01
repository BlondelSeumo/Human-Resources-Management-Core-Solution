<?php

namespace Gainhq\Installer\App\Hooks;;

use Gainhq\Installer\App\Helpers\Traits\InstanceCreator;
use Gainhq\Installer\App\Services\InstallationService;

class InstallationSaved
{
    use InstanceCreator;

    public function setEnvAndFinishInstallation()
    {
        return resolve(InstallationService::class)->finishInstallationAndSetupEnv();
    }

    public function finishInstallationWithoutSetEnv()
    {
        return resolve(InstallationService::class)->finishInstallation();
    }
}
