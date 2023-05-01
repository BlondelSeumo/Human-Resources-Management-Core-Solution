<?php

namespace Gainhq\Installer\App\Controllers\Setup;

use Gainhq\Installer\App\Controllers\Controller;
use Gainhq\Installer\App\Managers\Helper\PermissionsHelper;

class PermissionController extends Controller
{
    protected $helper;

    public function __construct(PermissionsHelper $helper)
    {
        $this->helper = $helper;
    }

    public function show()
    {
        return $this->helper->check();
    }
}
