<?php

namespace App\Http\Requests\Core\Auth\Role;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Auth\Role;

class RolePermissionRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return (new Role)
            ->attachPermissionRules();
    }
}
