<?php

namespace App\Http\Requests\Core\Auth\User;

use App\Helpers\Core\Traits\PasswordMessageHelper;
use App\Http\Requests\BaseRequest;
use App\Models\Core\Auth\User;

/**
 * Class UpdateUserPasswordRequest.
 */
class UpdateUserPasswordRequest extends BaseRequest
{
    use PasswordMessageHelper;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return (new User())->changePasswordRules();
    }
}
