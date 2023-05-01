<?php


namespace App\Mail\Tag;


use Illuminate\Support\Facades\URL;

class UserTag extends Tag
{
    use \App\Notifications\Traits\Tag;

    protected $user;

    public function __construct($user, $notifier = null, $receiver = null)
    {
        $this->user = $user;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->resourceurl = optional(tenant())->id ?
            route('support.tenant.users',optional(tenant())->is_single ? '' : ['tenant_parameter' => tenant()->short_name ])
            : route(config('notification.user_front_end_route_name'));
    }

    public function passwordReset($token)
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name,
            '{reset_password_url}' => URL::signedRoute('reset-password.index', ['token' => $token, 'email' => $this->user->email])
        ], $this->common());
    }

    public function invitation()
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name,
            '{invitation_url}' => URL::signedRoute('user-invite.index', ['invitation_token' => $this->user->invitation_token])
        ], $this->common());
    }

    public function invitationSubject()
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name
        ], $this->appNameAndLogo());
    }

    public function employeeCreateSubject()
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name
        ], $this->appNameAndLogo());
    }

    public function notification()
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name,
            '{email}' => optional($this->user)->email
        ], $this->common());
    }

    public function invitationCanceled()
    {
        return array_merge([
            '{name}' => optional($this->user)->full_name,
        ], $this->common());
    }

    public function invitationCanceledSubject()
    {
        return $this->commonForSubject();
    }
}
