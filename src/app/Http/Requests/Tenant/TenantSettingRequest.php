<?php

namespace App\Http\Requests\Tenant;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Setting\Traits\SettingRules;

class TenantSettingRequest extends BaseRequest
{
    use SettingRules;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = $this->createdRules();
        unset($rules['company_name']);
        unset($rules['company_logo']);
        unset($rules['app_name']);
        unset($rules['app_logo']);

        return array_merge($rules, [
            'tenant_name' => 'required',
            'tenant_logo' => 'nullable|image',
            'tenant_icon' => 'nullable|image',
            'tenant_banner' => 'nullable|image'
        ]);
    }

    public function messages()
    {
        return array_merge(
            parent::messages(),
            $this->settingMessages()
        );
    }
}
