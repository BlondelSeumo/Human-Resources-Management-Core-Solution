<?php

namespace App\Http\Controllers\Core\Setting;

use App\Http\Controllers\Controller;
use App\Repositories\Core\Status\StatusRepository;

class StatusController extends Controller
{

    public function index()
    {
        return resolve(StatusRepository::class)
            ->statuses(request()->get('type', ''));
    }
}
