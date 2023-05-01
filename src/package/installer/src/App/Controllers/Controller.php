<?php

namespace Gainhq\Installer\App\Controllers;

use Gainhq\Installer\App\Services\BaseService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller.
 */
class Controller extends BaseController
{


    /**
     * @var BaseService
     */
    protected $service;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
