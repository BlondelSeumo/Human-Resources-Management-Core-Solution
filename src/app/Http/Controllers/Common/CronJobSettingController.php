<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CronJobSettingController extends Controller
{
    public function index()
    {
        $php_path = exec("which php");
        $command = base_path().'/artisan schedule:run >> /dev/null 2>&1';
        $cpanel_command = exec("which php").' '.base_path().'/artisan schedule:run >> /dev/null 2>&1';

        return [
            'php_path' => $php_path,
            'cpanel_command' => $cpanel_command,
            'command' => $command,
        ];
    }
}
