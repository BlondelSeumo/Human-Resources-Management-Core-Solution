<?php

namespace App\Http\Controllers\Tenant\Assets;

use App\Exceptions\GeneralException;
use App\Export\AllAssetExport;
use App\Filters\Tenant\CompanyAssetFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Assets\CompanyAssetRequest;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Assets\CompanyAsset;
use Maatwebsite\Excel\Excel;

class CompanyAssetController extends Controller
{
    public function __construct(CompanyAssetFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index()
    {
        return CompanyAsset::filters($this->filter)
            ->with('type:id,name', 'user:id,first_name,last_name')
            ->latest('id')
            ->paginate(request()->get('per_page', 10));
    }

    public function store(CompanyAssetRequest $request)
    {
        CompanyAsset::query()
            ->create($request->only(
                'user_id',
                'name',
                'code',
                'serial_number',
                'type_id',
                'is_working',
                'date',
                'note'
            ));

        return created_responses('asset');
    }

    public function show(CompanyAsset $companyAsset)
    {
        $companyAsset->load('type:id,name');

        return $companyAsset;
    }

    public function update(CompanyAsset $companyAsset, CompanyAssetRequest $request)
    {
        $companyAsset->update($request->only(
                'user_id',
                'name',
                'code',
                'serial_number',
                'type_id',
                'is_working',
                'date',
                'note'
            ));
        return updated_responses('asset');
    }

    public function destroy(CompanyAsset $companyAsset)
    {
        try {
            $companyAsset->delete();
        } catch (\Exception $e) {
            throw new GeneralException(__t('can_not_delete_asset'));
        }

        return deleted_responses('asset');
    }

    public function employeeAssets(User $employee)
    {
        return CompanyAsset::filters($this->filter)
            ->where('user_id', $employee->id)
            ->with('type:id,name', 'user:id,first_name,last_name')
            ->latest('id')
            ->paginate(request()->get('per_page', 10));
    }

    public function exportAllAssets()
    {
        $assets = CompanyAsset::filters($this->filter)
            ->with('type:id,name', 'user:id,first_name,last_name')
            ->latest('id')
            ->get();

        $file_name = 'all-assets-'.date('Y-m-d').'.xlsx';

        return (new AllAssetExport($assets))->download($file_name, Excel::XLSX);

    }
}
