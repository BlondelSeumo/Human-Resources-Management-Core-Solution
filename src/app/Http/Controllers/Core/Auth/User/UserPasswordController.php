<?php

namespace App\Http\Controllers\Core\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Auth\User\UpdateUserPasswordRequest as Request;
use App\Models\Core\Auth\User;
use App\Services\Core\Auth\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Class UserPasswordController.
 */
class UserPasswordController extends Controller
{
    public function update(Request $request, User $user)
    {
        resolve(UserService::class)->validateIsNotDemoVersion();

        if (Hash::check($request->get('old_password'),  $user->password)){
            $user->update([
                'password' => $request->get('password')
            ]);
            auth()->loginUsingId($user->id);
            return updated_responses('password');
        }
        
        throw ValidationException::withMessages([
            'old_password' => trans('default.old_password_is_in_correct')
        ]);
    }
}
