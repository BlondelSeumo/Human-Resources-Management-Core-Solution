<?php

namespace App\Mail\Core\User;

use App\Mail\Tag\UserTag;
use App\Models\Core\Auth\User;
use App\Notifications\Core\Helper\NotificationTemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $user;

    protected $auth;

    public function __construct(User $user, $deliverySettings = [])
    {
        $this->user = $user;
        $this->auth = auth()->user();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = $this->template();

        $tag = new UserTag($this->user, $this->auth);

        return $this->view('notification.mail.user.template', [
            'template' => $template->parse(
                $tag->invitation()
            )
        ])->subject($template->parseSubject(
            method_exists($tag, 'invitationSubject') ? $tag->invitationSubject() : ['{name}' => $this->user->full_name]
        ));
    }


    public function template()
    {
        return NotificationTemplateHelper::new()
            ->on('user_invitation')
            ->mail();
    }

}
