<?php

namespace App\Http\Requests\Core\Auth\User;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Auth\User;

/**
 * Class ManageUserRequest.
 */
class UserRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \App\Exceptions\GeneralException
     */
    public function rules()
    {
        return $this->initRules(new User());
    }
}
