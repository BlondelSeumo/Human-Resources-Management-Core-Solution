<?php

namespace App\Http\Requests\Core\Auth\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class UserConfirmRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'invitation_token' => 'required|min:10',
            'first_name' => 'required|min:2',
            'password' => ['required', 'confirmed', 'regex:/^(?=[^\d]*\d)(?=[A-Z\d ]*[^A-Z\d ]).{8,}$/i']
        ];
    }
}
