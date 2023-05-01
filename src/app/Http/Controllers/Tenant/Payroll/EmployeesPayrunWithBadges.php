<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Services\Tenant\Payroll\DefaultPayrunService;
use Illuminate\Http\Request;

class EmployeesPayrunWithBadges extends Controller
{
    public function __construct(DefaultPayrunService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->followedByEmployee()
            ->with([
                'payrunSetting',
                'payrunBeneficiaries'
            ])->paginate();
    }
}
