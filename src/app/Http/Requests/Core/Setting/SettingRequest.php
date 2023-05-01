<?php


namespace App\Http\Requests\Core\Setting;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Setting\Traits\SettingRules;

class SettingRequest extends BaseRequest
{
    use SettingRules;
    /**
     * @return array
     * @throws \App\Exceptions\GeneralException
     */
    public function rules()
    {
        return $this->createdRules();
    }

    public function messages()
    {
        return $this->settingMessages();
    }
}
