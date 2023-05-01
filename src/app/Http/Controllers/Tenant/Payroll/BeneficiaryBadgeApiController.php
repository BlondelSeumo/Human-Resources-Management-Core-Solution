<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Payroll\Beneficiary;
use Illuminate\Http\Request;

class BeneficiaryBadgeApiController extends Controller
{
    public function index()
    {
        return Beneficiary::query()
            ->where('is_active', 1)->get(['id', 'name', 'type']);
    }
}
