<?php

namespace App\Services\Tenant;
use App\Models\Tenant\Attendance\Attendance;

class AttendanceExportService extends TenantService
{

    public function __construct(Attendance $attendance)
    {
        $this->model = $attendance;
    }

}