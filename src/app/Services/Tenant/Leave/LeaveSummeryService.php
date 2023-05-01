<?php

namespace App\Services\Tenant\Leave;

use App\Models\Tenant\Leave\Leave;
use App\Services\Tenant\TenantService;

class LeaveSummeryService extends TenantService
{

    public function __construct(Leave $leave)
    {
        $this->model = $leave;
    }


}
