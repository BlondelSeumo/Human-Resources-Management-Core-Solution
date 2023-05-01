<?php

namespace Gainhq\Installer\App\Models\Core;

use Gainhq\Installer\App\Models\Core\Traits\TranslatedNameTrait;
use Gainhq\Installer\App\Models\Core\Traits\TypeRelationship;

class Permission extends BaseModel
{
    protected $appends = ['translated_name'];

    use TranslatedNameTrait, TypeRelationship;

    protected $fillable = [
        'type_id', 'name', 'group_name'
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission', 'permission_id', 'role_id');
    }
}
