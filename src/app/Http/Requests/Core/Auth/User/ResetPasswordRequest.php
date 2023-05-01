<?php

namespace App\Http\Requests\Core\Auth\User;

use App\Helpers\Core\Traits\PasswordMessageHelper;
use App\Models\Core\Auth\Traits\Rules\UserRules;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    use UserRules, PasswordMessageHelper;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->resetPasswordRules();
    }
}
