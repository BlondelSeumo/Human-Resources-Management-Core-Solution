<?php

namespace Gainhq\Installer\App\Models\Core;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['user_id', 'gender', 'date_of_birth', 'address', 'contact'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
