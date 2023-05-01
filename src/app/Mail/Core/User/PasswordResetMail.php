<?php

namespace App\Mail\Core\User;

use App\Mail\Tag\UserTag;
use App\Models\Core\Auth\User;
use App\Notifications\Core\Helper\NotificationTemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $user;

    protected $token;

    public function __construct(User $user, $token, $deliverySettings = [])
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function build()
    {
        $template = $this->template();

        $tag = new UserTag($this->user);

        return $this->view('notification.mail.user.template', [
            'template' => $template->parse(
                $tag->passwordReset($this->token)
            )
        ])->subject($template->parseSubject(
            method_exists($tag, 'passwordResetSubject') ? $tag->passwordResetSubject() : ['{name}' => $this->user->full_name]
        ));
    }

    public function template()
    {
        return NotificationTemplateHelper::new()
            ->on('password_reset')
            ->mail();
    }
}
