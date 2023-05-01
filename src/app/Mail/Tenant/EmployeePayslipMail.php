<?php

namespace App\Mail\Tenant;

use App\Mail\Tag\EmployeeTag;
use App\Mail\Tag\UserTag;
use App\Notifications\Core\Helper\NotificationTemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeePayslipMail extends Mailable
{
    use Queueable, SerializesModels;

    public object $user;

    public string $template;

    public $subject;

    public $attachment;

    public function __construct($user, $filePath)
    {
        $tag = new EmployeeTag($user, auth()->user(), $user);

        $this->user = $user;

        $this->attachment = $filePath;

        $template = $this->template();

        $this->template = optional($template)->parse(
            method_exists($tag, 'payslipGenerate') ? $tag->payslipGenerate() : ['{name}' => optional($user)->full_name]
        );

        $this->subject = optional($template)->parseSubject(
            method_exists($tag, 'payslipGenerate') ? $tag->payslipGenerate() : ['{name}' => optional($user)->full_name]
        );
    }


    public function build()
    {
        return $this->view('notification.mail.user.template', [
            'template' => $this->template
        ])->attach($this->attachment, [
            'as' => 'Payslip for '.$this->user->full_name.' ('.($this->user->profile ? $this->user->profile->employee_id : 'uid').').pdf',
            'mime' => 'application/pdf',
        ])->subject($this->subject);
    }

    public function template()
    {
        return NotificationTemplateHelper::new()
            ->on('employee_payslip_generate')
            ->mail();
    }
}
