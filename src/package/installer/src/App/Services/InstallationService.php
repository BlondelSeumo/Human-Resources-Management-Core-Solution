<?php


namespace Gainhq\Installer\App\Services;

use Gainhq\Installer\App\Managers\DatabaseManager;
use Gainhq\Installer\App\Managers\EnvironmentManager;
use Gainhq\Installer\App\Managers\FinalInstallManager;
use Gainhq\Installer\App\Managers\Helper\PermissionsHelper;
use Gainhq\Installer\App\Managers\Helper\Requirements;
use Gainhq\Installer\App\Managers\PurchaseCodeManager;
use Gainhq\Installer\App\Managers\StorageManager;
use Gainhq\Installer\App\Managers\Validator\PurchaseCodeValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;

class InstallationService extends BaseService
{
    protected $requirement;
    protected $permission;
    protected $environment;
    protected $userService;
    protected $brandService;
    protected $purchaseCodeValidator;
    protected $databaseManager;
    protected $purchaseCodeManager;
    protected $manager;
    protected $storage;

    public function __construct(
        Requirements $requirements,
        PermissionsHelper $permission,
        EnvironmentManager $environment,
        UserService $userService,
        PurchaseCodeValidator $codeValidator,
        DatabaseManager $databaseManager,
        PurchaseCodeManager $codeManager,
        FinalInstallManager $manager,
        StorageManager $storage

    )
    {
        $this->requirement = $requirements;
        $this->permission = $permission;
        $this->environment = $environment;
        $this->userService = $userService;
        $this->purchaseCodeValidator = $codeValidator;
        $this->databaseManager = $databaseManager;
        $this->purchaseCodeManager = $codeManager;
        $this->manager = $manager;
        $this->storage = $storage;
    }

    public function validatePurchaseCode(Request $request = null)
    {
        $purchase_code = $request ? $request->get('code') : env('PURCHASE_CODE');

        throw_if(
            !$this->purchaseCodeValidator->validate($purchase_code),
            ValidationException::withMessages(['code' => [trans('default.invalid_purchase_code')]])
        );

        return $this;
    }

    public function setDatabaseConfig()
    {
        $this->databaseManager->setConfig();
        return $this;
    }

    public function storeEnvironment(Request $request)
    {
        $this->environment->saveFileWizard($request);
        return $this;
    }

    public function clearCache()
    {
        $this->manager->clear();
        return $this;
    }

    public function migrate()
    {
        $this->databaseManager->migrate();
        return $this;
    }

    public function storePurchaseCode()
    {
        $this->purchaseCodeManager->store(
            env('PURCHASE_CODE')
        );

        return $this;
    }

    public function seedStatusAndType()
    {
        $this->databaseManager->seedEssential();
        return $this;
    }

    public function createUser()
    {
        $user = $this->userService
            ->create()
            ->getModel();

        $this->databaseManager->seedRole();

        /** @var User $user */
        $user->assignRole(config('access.users.app_admin_role'));

        return $this;
    }

    public function seedDatabase()
    {
        Artisan::call('db:seed', [
            '--class' => config('installer.seeder.setup_seeder'),
            '--force' => true
        ]);
        return $this;
    }

    public function linkStorage()
    {
        $this->storage->link();
        return $this;
    }
    public function finishInstallationAndSetupEnv()
    {
        $this->environment->setEnvironmentValue('APP_INSTALLED', 'true');
        $this->manager->finish();
        return $this;
    }

    public function finishInstallation()
    {
        $this->manager->finish();
        return $this;
    }

    public function setEnvironmentValue()
    {
        $this->environment->setEnvironmentValue('APP_INSTALLED', 'true');
        return $this;
    }

    public function getHead($config)
    {
        return array('Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Charset' => 'ISO-8859-2,utf-8;q=0.7,*;q=0.3',
            'Accept-Encoding' => 'gzip,deflate,sdch',
            'Accept-Language' => 'ro-RO,ro;q=0.8,en-US;q=0.6,en;q=0.4',
            'Cache-Control' => 'max-age=0',
            'Connection' => 'keep-alive',
            'Cookie' => 'datr=hroHTi2NZk2KleOaswb03Q_Q; lu=gg9lJcPeInHt6hnut7bviqQg; locale=en_US; e=n; L=2; c_user=100000596376783; sct=1309129360; xs=2%3Ad05dd80e364608525dd664ad73f6483f; act=1309410851554%2F5; presence=EM309410852L4N0_5dEp_5f1B00596376783F1X309410852168Y0Z11G309410768PCC',
            'Host' => $config['update_url'],
            'User-Agent' => $_SERVER['HTTP_USER_AGENT']);
    }
}
