<?php

namespace App\Mail\Core\User;

use App\Mail\Tag\UserTag;
use App\Notifications\Core\Helper\NotificationTemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvitationCancelMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $user;

    public $template;

    public $subject;

    public function __construct($user)
    {
        $tag = new UserTag($user, auth()->user(), $user);
        $template = $this->template();

        $this->user = $user;
        $this->template = optional($template)->parse(
            method_exists($tag, 'invitationCanceled') ? $tag->invitationCanceled() : ['{name}' => optional($user)->full_name]
        );

        $this->subject = optional($template)->parseSubject(
            method_exists($tag, 'invitationCanceledSubject') ? $tag->invitationCanceledSubject() : ['{name}' => optional($user)->full_name]
        );
    }

    public function build()
    {
        return $this->view('notification.mail.user.template', [
            'template' => $this->template
        ])->subject($this->subject);
    }

    public function template()
    {
        return NotificationTemplateHelper::new()
            ->on('user_invitation_canceled')
            ->mail();
    }
}
