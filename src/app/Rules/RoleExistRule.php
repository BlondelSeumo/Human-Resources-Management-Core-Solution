<?php

namespace App\Rules;

use App\Helpers\Traits\MakeArrayFromString;
use App\Models\Core\Auth\Role;
use Illuminate\Contracts\Validation\Rule;

class RoleExistRule implements Rule
{

    use MakeArrayFromString;

    public function passes($attribute, $value)
    {
        $values = $this->makeArray($value);
        $roles = Role::query()
            ->whereIn('name', $values)
            ->pluck('name')
            ->toArray();

         return count($roles) == count($values);
    }


    public function message()
    {
        return trans('default.is_invalid_message', ['subject' => __t('role')]);
    }
}
