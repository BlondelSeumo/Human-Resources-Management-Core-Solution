<?php

namespace App\Mail\Tenant;

use App\Mail\Tag\EmployeeTag;
use App\Models\Core\Auth\User;
use App\Notifications\Core\Helper\NotificationTemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeePasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public User $user;
    public $auth;
    protected $token;

    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->auth = auth()->user();
        $this->token = $token;
    }


    public function build()
    {
        $template = $this->template();

        $tag = new EmployeeTag($this->user, $this->auth, $this->user);

        return $this->view('notification.mail.template', [
            'template' => $template->parse(
                $tag->updatePassword($this->token)
            )
        ])->subject($template->parseSubject(
            method_exists($tag, 'common') ? $tag->common() : ['{name}' => $this->user->full_name]
        ));
    }

    public function template()
    {
        return NotificationTemplateHelper::new()
            ->on('employee_password_reset')
            ->mail();
    }
}
