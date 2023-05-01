<?php

namespace App\Http\Controllers\Core\Auth\User;

use App\Http\Controllers\Controller;
use App\Models\Core\Auth\Profile;
use App\Services\Core\Auth\UserService;
use App\Http\Requests\Core\Auth\User\UserSettingRequest as Request;

class UserUpdateController extends Controller
{
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return Profile::query()
            ->where('user_id', auth()->id())
            ->first();
    }

    public function update(Request $request)
    {
        $this->service->validateIsNotDemoVersion();

        auth()->user()->update(
            $request->only('first_name', 'last_name', 'email')
        );

        Profile::query()->updateOrCreate([
            'user_id' => auth()->id()
        ], array_merge(
            ['user_id' => auth()->id()],
            $request->only('gender', 'date_of_birth', 'address', 'contact', 'about_me')
        ));

        return updated_responses('profile');
    }
}
