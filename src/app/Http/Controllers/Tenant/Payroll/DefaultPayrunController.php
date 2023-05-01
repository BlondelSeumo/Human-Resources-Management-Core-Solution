<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Services\Tenant\Payroll\DefaultPayrunService;

class DefaultPayrunController extends Controller
{
    public function __construct(DefaultPayrunService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $defaultSettings = $this->service->defaultSettings();

        return [
            "default" => [
                'count_employee' => $this->service->countFollowedBySettings(),
                'consider_type' => $defaultSettings->setting->consider_type,
                'period' => $defaultSettings->setting->payrun_period,
                "time_range" => $this->service->getDefaultPayrunTimeRange(),
                "beneficiaries" => $this->service->getDefaultBeneficiaryBadges(),
                "consider_overtime" => $defaultSettings->setting->consider_overtime,
            ],
            'employee' => [
                'count_employee' => $this->service->countFollowedByEmployee(),
                'consider_type' => $this->service->employeesConsiderType(),
                'period' => $this->service->employeesPayrunPeriod(),
                "time_range" => $this->service->employeePayrunTimeRange(),
                "restricted_badge_user" => $this->service->restrictedUserForBadge(),
                "consider_overtime" => $this->service->employeesPayrunConsiderOvertime()
            ]
        ];
    }

    public function employees()
    {
        return $this->service->followedByEmployee()
            ->select('id', 'first_name', 'last_name')
            ->with([
                'payrunSetting',
                'payrunBeneficiaries',
                'payrunBeneficiaries.beneficiary'
            ])->paginate(request()
                ->get('per_page', 10));
    }
}
