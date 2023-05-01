<?php

namespace App\Http\Requests\Core\Auth\User;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Auth\User;

class UserRoleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \App\Exceptions\GeneralException
     */
    public function rules()
    {
        return (new User)
            ->attachRoleRules();
    }
}
