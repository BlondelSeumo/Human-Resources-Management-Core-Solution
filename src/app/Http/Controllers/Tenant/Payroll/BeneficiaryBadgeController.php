<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\BeneficiaryBadgeFilter;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Payroll\Beneficiary;
use App\Services\Tenant\Payroll\BeneficiaryBadgeService;
use Illuminate\Http\Request;

class BeneficiaryBadgeController extends Controller
{
    public function __construct(BeneficiaryBadgeService $beneficiaryService, BeneficiaryBadgeFilter $beneficiaryFilter)
    {
        $this->service = $beneficiaryService;
        $this->filter = $beneficiaryFilter;
    }

    public function index()
    {
        return $this->service
            ->filters($this->filter)
            ->latest('id')
            ->paginate(request()->get('per_page', 10));
    }


    public function store(Request $request)
    {
        $this->service
            ->setAttributes(\request()->only('name', 'type', 'description', 'is_active'))
            ->validate()
            ->save();

        return created_responses('beneficiary_badge');
    }


    public function show(Beneficiary $beneficiary)
    {
        return $beneficiary;
    }


    public function update(Request $request, Beneficiary $beneficiary)
    {
        $this->service
            ->setModel($beneficiary)
            ->setAttributes(\request()->only('name', 'type', 'description', 'is_active'))
            ->validate()
            ->validateConstrain()
            ->save();

        return updated_responses('beneficiary_badge');
    }


    public function destroy(Beneficiary $beneficiary)
    {
        try {
            $beneficiary->delete();
        } catch (\Exception $e) {
            throw new GeneralException(__t('can_not_delete_used_badge'));
        }

        return deleted_responses('beneficiary');
    }
}
