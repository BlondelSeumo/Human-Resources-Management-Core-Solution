<?php

namespace App\Mail\Tenant;

use App\Mail\Tag\EmployeeTag;
use App\Notifications\Core\Helper\NotificationTemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeInvitationCancelMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public object $user;

    public string $template;

    public $subject;

    public function __construct($user)
    {
        $tag = new EmployeeTag($user, auth()->user(), $user);

        $this->user = $user;

        $template = $this->template();

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
            ->on('employee_invitation_canceled')
            ->mail();
    }
}
