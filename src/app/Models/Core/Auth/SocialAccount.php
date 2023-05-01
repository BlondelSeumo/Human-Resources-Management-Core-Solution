<?php

namespace App\Models\Core\Auth;

use App\Models\Core\BaseModel;

class SocialAccount extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'social_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
        'token',
        'avatar',
    ];
}
