<?php

namespace App\Http\Requests\Core\Auth\User;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Auth\User;
use Illuminate\Foundation\Http\FormRequest;

class UserSettingRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return (new User)
            ->userSettingsRules();
    }
}
