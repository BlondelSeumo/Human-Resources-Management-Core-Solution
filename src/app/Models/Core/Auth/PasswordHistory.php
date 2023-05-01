<?php

namespace App\Models\Core\Auth;

use Illuminate\Database\Eloquent\Model;


class PasswordHistory extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'password_histories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['password'];
}
