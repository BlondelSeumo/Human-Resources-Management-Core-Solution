<?php

namespace Gainhq\Installer\App\Controllers\Setup;

use Gainhq\Installer\App\Controllers\Controller;
use Gainhq\Installer\App\Hooks\InstallationSaved;
use Gainhq\Installer\App\Managers\Helper\PermissionsHelper;
use Gainhq\Installer\App\Request\AdminRequest;
use Gainhq\Installer\App\Request\EnvRequest;
use Gainhq\Installer\App\Services\AdditionalRequirementService;
use Gainhq\Installer\App\Services\InstallationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EnvironmentController extends Controller
{
    protected $permission;
    protected $additionalRequirementService;

    public function __construct(
        InstallationService $service,
        PermissionsHelper $permission,
        AdditionalRequirementService $additionalRequirementService)
    {
        $this->service = $service;
        $this->permission = $permission;
        $this->additionalRequirementService = $additionalRequirementService;
    }

    public function index()
    {
        return view('installer::database_configuration');
    }

    public function additionalIndex()
    {
        return view('installer::additional_requirement');
    }

    public function store(EnvRequest $request)
    {
        $this->service
            ->setDatabaseConfig()
            ->storeEnvironment($request);

        return response()->json(['status' => true, 'message' => trans('default.database_configured_successfully')]);

    }

    public function show()
    {
        if (env('PURCHASE_CODE')) {
            return view('installer::admin_info');
        }

        return redirect()->route('environment.index');
    }

    public function update(AdminRequest $request)
    {
        $this->service
            ->clearCache()
            ->migrate()
            ->storePurchaseCode()
            ->seedStatusAndType()
            ->createUser()
            ->seedDatabase()
            ->finishInstallation();

        $message = trans('default.admin_info_saved_successfully');

        if (!config('installer.include_option.email_set_up')) {
            $message = trans('default.app_installed_successfully');
            $this->service->setEnvironmentValue();
        }

        return response()->json(['status' => true, 'message' => $message]);

    }

    public function generateUrl(Request $request)
    {
        $code = trim($request->code);
        $type = 'verification';
        $domain_name = request()->getHost();
        $config = config('gain');
        $attach = isset($config['use_update_route']) ? '/' . $config['use_update_route'] : '';
        return "{$config['update_url']}{$attach}/{$type}/{$config['app_id']}?domain_name={$domain_name}&purchase_key={$code}&app_version={$config['app_version']}";
    }

    public function getHostName()
    {
        try {
            $hostName = gethostname();
        } catch (\Exception $exception) {
            $hostName = 'localhost';
        }
        return response()->json(['status' => true, 'host' => $hostName]);
    }

    public function emailSetup()
    {
        if (env('PURCHASE_CODE')) {
            return view('installer::email_setup');
        }

        return redirect()->route('environment.index');
    }

    public function broadcastSetup()
    {
        if (env('PURCHASE_CODE')) {
            return view('installer::broadcast_setup');
        }

        return redirect()->route('environment.index');
    }

    public function skipBroadCast()
    {
        InstallationSaved::new()
            ->setEnvAndFinishInstallation();

        $message = trans('default.app_installed_successfully');
        return response()->json(['status' => true, 'message' => $message]);
    }

    public function skipEmailSetup()
    {
        if (config('installer.skip_option.email_set_up')){

            //Checking next redirect option is enabled
            if(config('installer.include_option.pusher_set_up')){
                //Prepare to redirect to pusher set
                InstallationSaved::new()->finishInstallationWithoutSetEnv();

                $message = trans('default.email_setup_saved_successfully');

            }else{
                //Installation complete
                InstallationSaved::new()->setEnvAndFinishInstallation();
                $message = trans('default.app_installed_successfully');

            }
            return response()->json(['status' => true, 'message' => $message]);

        }else{
            return response()->json(['status' => false, 'message' => trans('default.do_not_have_permission')]);
        }
    }

    public function additionalRequirements()
    {
        $data = $this->additionalRequirementService
            ->checkSymlinkPermission();
        return response()->json(['status' => true, 'data' => $data], 200);
    }

    public function clearCache()
    {
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return response()->json(['status' => true, 'message' => 'Language cache has been cleared' ], 200);
    }
}
