<?php


namespace Gainhq\Installer\App\Models\Core\Traits;

trait RoleRules
{
    public function createdRules()
    {
        return [
            'name' => 'required|min:2|max:195',
            'type_id' => [
                'required'
            ]
        ];
    }

    public function attachPermissionRules()
    {
        return [
            'permissions' => 'required'
        ];
    }
}
