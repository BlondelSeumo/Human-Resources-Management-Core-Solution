<?php

namespace Gainhq\Installer\App\Controllers\Setup;

use Gainhq\Installer\App\Controllers\Controller;
use Gainhq\Installer\App\Managers\Helper\PermissionsHelper;
use Gainhq\Installer\App\Managers\Helper\Requirements;

class AppInstallController extends Controller
{
    protected $requirements;

    protected $permission;

    public function __construct(Requirements $requirements, PermissionsHelper $permission)
    {
        $this->requirements = $requirements;
        $this->permission = $permission;
    }

    public function index()
    {
        if ($this->requirements->isSupported() && $this->permission->isSupported()) {
            return redirect()->route('app.environment');
        }
        return view('install.index');
    }

    public function show()
    {
        return array_merge(array_merge([
            'php' => $this->requirements->checkPhpVersion()
        ], $this->requirements->check()), [
            'permissions' => $this->permission->check()
        ]);
    }
}
