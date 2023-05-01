<?php

namespace App\Models\Tenant\Utility;

use App\Models\Core\Auth\User;
use App\Models\Tenant\TenantModel;

class Comment extends TenantModel
{
    protected $fillable = ['commentable_type', 'commentable_id', 'user_id', 'type', 'comment', 'parent_id'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $hidden = [
        'commentable_type',
        'commentable_id'
    ];
}
