<?php

namespace App\Http\Requests\Core\Setting;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Setting\NotificationSetting;

class NotificationSettingRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \App\Exceptions\GeneralException
     */
    public function rules()
    {
        return $this->initRules(new NotificationSetting());
    }

    public function messages()
    {
        return [
            'audiences.0.audience_type.required_if' => trans('validation.required', ['attribute' => trans('default.audience_type')]),
            'audiences.1.audience_type.required_if' => trans('validation.required', ['attribute' => trans('default.audience_type')]),
            'audiences.1.audiences.required_if' => trans('validation.required', ['attribute' => trans('default.audience')]),
            'audiences.0.audiences.required_if' => trans('validation.required', ['attribute' => trans('default.audience')]),
        ];
    }
}
