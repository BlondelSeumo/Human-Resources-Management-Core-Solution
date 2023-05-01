<?php

namespace App\Http\Requests\Tenant\Assets;

use App\Http\Requests\BaseRequest;

class CompanyAssetRequest extends BaseRequest
{
    public function rules()
    {

        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string',
            'code' => 'nullable|string|unique:company_assets,code,'.optional($this)->id,
            'serial_number' => 'nullable|string|unique:company_assets,serial_number,'.optional($this)->id,
            'type_id' => 'required|exists:company_asset_types,id',
            'is_working' => 'required|in:yes,no,maintenance',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'is_working' => 'working status',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Select employee to assign asset.',
            'type_id.required' => 'Select the type of asset.',
        ];
    }
}
