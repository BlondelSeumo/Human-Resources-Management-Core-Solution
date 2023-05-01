<?php


namespace Gainhq\Installer\App\Models\Core\Traits;


trait UserRules
{
    public function createdRules()
    {
        return [
            'first_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'min:8', 'regex:/^(?=[^\d]*\d)(?=[A-Z\d ]*[^A-Z\d ]).{8,}$/i'],
            'roles' => ['nullable', 'array'],
        ];
    }

    public function updatedRules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'nullable|min:2',
        ];
    }

    public function attachRoleRules()
    {
        return [
            'roles' => 'required'
        ];
    }

    public function userSettingsRules()
    {
        return [
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'first_name' => 'nullable|min:2',
            'email' => 'required|email|unique:users,email,'.auth()->id().',id',
        ];
    }

    public function thumbnailRules()
    {
        return [
            'profile_picture' => 'required|image'
        ];
    }

    public function changePasswordRules()
    {
        return [
            'old_password' => 'required|min:6',
            'password' => 'required|min:8|confirmed|regex:/^(?=[^\d]*\d)(?=[A-Z\d ]*[^A-Z\d ]).{8,}$/i',
        ];
    }

    public function loginRules()
    {
        return [
            'email' => 'required|min:5|email',
            'password'=> 'required'
        ];
    }

    public function resetPasswordRules()
    {
        return [
            'password' => 'required|min:8|confirmed|regex:/^(?=[^\d]*\d)(?=[A-Z\d ]*[^A-Z\d ]).{8,}$/i',
            'token' => 'required|min:10',
            'email' => 'required|email'
        ];
    }

}
