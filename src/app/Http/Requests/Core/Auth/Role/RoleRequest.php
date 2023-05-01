<?php


namespace App\Http\Requests\Core\Auth\Role;


use App\Http\Requests\BaseRequest;
use App\Models\Core\Auth\Role;

class RoleRequest extends BaseRequest
{

    public function rules()
    {
        return $this->initRules(new Role());
    }
}
