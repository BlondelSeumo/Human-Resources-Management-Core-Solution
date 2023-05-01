<?php

namespace Gainhq\Installer\App\Controllers\Setup;

use Gainhq\Installer\App\Exceptions\GeneralException;
use Gainhq\Installer\App\Managers\FileManager;
use Gainhq\Installer\App\Managers\FinalInstallManager;
use Gainhq\Installer\App\Managers\Helper\PermissionsHelper;
use Gainhq\Installer\App\Managers\Helper\SetIiiTrait;
use Gainhq\Installer\App\Managers\Helper\UrlHelper;
use Gainhq\Installer\App\Managers\Helper\ValidatePHPVersion;
use Gainhq\Installer\App\Controllers\Controller;
use Gainhq\Installer\App\Managers\UpdateManager;
use Gainhq\Installer\App\Repositories\Setting\SettingRepository;
use Illuminate\Http\Request;

class AppUpdateController extends Controller
{
    use UrlHelper, SetIiiTrait, ValidatePHPVersion;

    protected $permission;
    protected $updateManager;
    protected $fileManager;
    protected $manager;

    public function __construct(
        PermissionsHelper $permissionsHelper,
        UpdateManager $updateManager,
        FileManager $fileManager,
        FinalInstallManager $manager
    )
    {
        $this->permission = $permissionsHelper;
        $this->updateManager = $updateManager;
        $this->fileManager = $fileManager;
        $this->manager = $manager;
    }

    public function index()
    {

        throw_if(
            $this->permission->check(['public/' => 'writeable', '/' => 'writeable'])['errors'],
            new GeneralException(trans('default.public_directory_must_be_writeable_to_update_the_app'))
        );

        //Make curl error
        if(config('installer.manual_update')){
            return response()->json([
                'message' => json_encode([
                    'message_1' => 'Curl request test failed'
                ])
            ], 402);
        }

        $updates = $this->updateManager->updates();

        if ($updates->status) {
            $spliceIndex = array_search(config('gain.app_version'), array_column((array)$updates, 'version'));

            $result = collect($updates->result)->map(function ($version) {
                $version->version = str_replace('.zip', '', $version->version);
                return $version;
            })->toArray();

            if (!$spliceIndex)
                return response()->json(['status' => true, 'result' => $result]);

            $result = collect($result)->filter(function ($value, $index) use ($spliceIndex) {
                return $index >= $spliceIndex;
            })->toArray();

            return response()->json(['status' => true, 'result' => $result]);

        }
        return response()->json((array)$updates, 402);

    }

    public function update($version)
    {
        $this->fileManager->removeCachedFile();
        $this->setMemoryLimit();
        $this->setExecutionTime();
        $this->validatePHPVersion();

        throw_if(
            !array_search('zip', get_loaded_extensions()),
            new GeneralException(trans('default.install_zip_extension'), 404)
        );

        $nextVersion = $this->getNextReadyToInstallVersion($version);

        throw_if(
            !$nextVersion['check'],
            new GeneralException(trans('default.please_install_version_first', ['number' => $nextVersion['version']]))
        );

        $this->fileManager->extract($version);

        return response()->json(['status' => true, 'message' => "$version installed successfully."]);
    }

    public function getNextReadyToInstallVersion($version)
    {
        $available_updates = $this->index()->getData();

        throw_if(!$available_updates->status, new GeneralException(trans('default.invalid_purchase_code')));

        $updates = $available_updates->result;

        $installable = array_search($version, array_column($updates, 'version'));

        if ($installable === 0)
            return ['version' => $updates[$installable]->version, 'check' => true];

        return ['version' => $updates[0]->version, 'check' => false];
    }

    public function getUpdateUrl()
    {

        $code = $this->getPurchaseCode();

        $type = 'verification';
        $domain_name = request()->getHost();
        $config = config('gain');
        $attach = isset($config['use_update_route']) ? '/' . $config['use_update_route'] : '';
        return "{$config['update_url']}{$attach}/{$type}/{$config['app_id']}?domain_name={$domain_name}&purchase_key={$code}&app_version={$config['app_version']}";
    }

    public function urlInfo()
    {
        return [
            'purchaseCode' => $this->getPurchaseCode(),
            'appId' => config('gain.app_id'),
            'currentAppVersion' => config('gain.app_version')
        ];
    }

    public function manualUpdate(Request $request)
    {
        throw_if($this->getPurchaseCode() !== $request->purchaseCode,
            new GeneralException(trans('default.invalid_purchase_code'), 401));

        $version = $request->next_version;
        $this->uploadFileInPublic($request);

        $this->fileManager->extract($version);
        $this->manager->finishUpdate();
        return response()->json(['status' => true, 'message' => "$version installed successfully."]);
    }

    public function uploadFileInPublic($request)
    {
        $request->validate([
            'update_file' => 'required|mimes:zip'
        ]);

        $fileName = $request->update_file->getClientOriginalName();

        $request->update_file->move(public_path('updates/'), $fileName);
    }

    public function generateDownloadFileUrl()
    {
        return [
            'url' => config('gain.update_url') . '/v2/manual-download?purchaseCode=' . env('PURCHASE_CODE') . '&appId=' . config('gain.app_id') . '&currentAppVersion=' . config('gain.app_version')
        ];
    }

    public function getPurchaseCode()
    {
        $settings = resolve(SettingRepository::class)
            ->createSettingInstance('purchase_code', 'purchase_code');

        $code = env('PURCHASE_CODE') ? env('PURCHASE_CODE') : $settings->value;
        $code = trim($code);
        return $code;

    }
}
