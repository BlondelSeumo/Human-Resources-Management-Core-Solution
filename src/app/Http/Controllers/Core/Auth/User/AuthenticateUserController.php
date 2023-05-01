<?php

namespace App\Http\Controllers\Core\Auth\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthenticateUserController extends Controller
{
    public function show()
    {
        return auth()->user()->load('roles:id,name', 'profile:id,user_id,gender,date_of_birth,address,contact,about_me', 'profilePicture', 'status:id,name,class');
    }
}
