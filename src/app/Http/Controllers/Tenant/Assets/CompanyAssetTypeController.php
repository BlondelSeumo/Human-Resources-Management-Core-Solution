<?php

namespace App\Http\Controllers\Tenant\Assets;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\CompanyAssetTypeFilter;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Assets\CompanyAssetType;
use Illuminate\Http\Request;

class CompanyAssetTypeController extends Controller
{
    public function __construct(CompanyAssetTypeFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index()
    {
        return CompanyAssetType::query()
            ->filters($this->filter)
            ->orderBy('id', request()->get('orderBy', 'desc'))
            ->paginate(request()->get('per_page', 10));
    }

    public function selectable()
    {
        return CompanyAssetType::query()
            ->orderBy('id', request()->get('orderBy', 'desc'))
            ->get();
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        CompanyAssetType::query()->create($request->only('name'));

        return created_responses('asset_types');
    }


    public function show(CompanyAssetType $company_asset_type): CompanyAssetType
    {
        return $company_asset_type;
    }


    public function update(Request $request, CompanyAssetType $company_asset_type)
    {
        $request->validate([
            'name' => 'required|string',
        ]);
        $company_asset_type->update($request->only('name'));

        return updated_responses('asset_types');
    }


    public function destroy(CompanyAssetType $company_asset_type)
    {
        try {
            $company_asset_type->delete();
        } catch (\Exception $e) {
            throw new GeneralException(__t('can_not_delete_used_asset_type'));
        }

        return deleted_responses('asset_types');
    }
}