<?php


namespace App\Mail\Tag;


use Illuminate\Support\Facades\URL;

class EmployeeTag extends UserTag
{
    public function invitation()
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name,
            '{invitation_url}' => URL::signedRoute('user-invite.index', ['invitation_token' => $this->user->invitation_token])
        ], $this->common());
    }

    public function payslipGenerate()
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name,
        ], $this->common());
    }

    public function employeeCreate()
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name,
            '{email}' => optional($this->user)->email,
            '{password}' => optional($this->user)->tempPass,
            '{resource_url}' => route('users.login.index')
        ], $this->common());
    }

    public function terminateEmployee(): array
    {
        return $this->common();
    }

    public function terminateEmployeeSubject(): array
    {
        return $this->commonForSubject();
    }

    public function updatePassword($token)
    {
        return array_merge([
            '{invitation_url}' => URL::signedRoute('reset-password.index', ['token' => $token, 'email' => $this->user->email])
        ],$this->common());
    }

}