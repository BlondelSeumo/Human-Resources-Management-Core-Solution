<?php

namespace App\Mail\Tenant;

use App\Mail\Tag\EmployeeTag;
use App\Models\Core\Auth\User;
use App\Notifications\Core\Helper\NotificationTemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeCreateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;

    public $auth;

    protected $tempPass;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->tempPass = optional($this->user)->tempPass;
        $this->auth = auth()->user();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->user->tempPass = $this->tempPass;
        $template = $this->template();

        $tag = new EmployeeTag($this->user, $this->auth, $this->user);

        return $this->view('notification.mail.template', [
            'template' => $template->parse(
                $tag->employeeCreate()
            )
        ])->subject($template->parseSubject(
            method_exists($tag, 'invitationSubject') ? $tag->employeeCreateSubject() : ['{name}' => $this->user->full_name]
        ));
    }

    public function template()
    {
        return NotificationTemplateHelper::new()
            ->on('user_create')
            ->mail();
    }
}