<?php

namespace App\Http\Controllers;

use App\Filters\FilterBuilder;
use App\Services\Core\BaseService;
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
     * @var FilterBuilder
     */
    protected $filter;

    /**
     * @var BaseService
     */
    protected $service;

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
