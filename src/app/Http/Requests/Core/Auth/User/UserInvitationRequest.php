<?php

namespace App\Http\Requests\Core\Auth\User;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UserInvitationRequest extends BaseRequest
{

    protected function prepareForValidation()
    {
        if ($this->has('roles')) {
            $roles = is_array($this->get('roles')) ? $this->get('roles') : [$this->get('roles')];
            $this->merge([
                'roles' => $roles
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email',
            'roles' => [
                'required',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->whereIn('id', $this->roles);
                })
            ]
        ];
    }
}
