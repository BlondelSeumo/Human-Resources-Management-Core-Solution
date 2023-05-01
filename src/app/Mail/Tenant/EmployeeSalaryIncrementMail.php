<?php

namespace App\Mail\Tenant;

use App\Mail\Tag\EmployeeTag;
use App\Mail\Tag\SalaryTag;
use App\Notifications\Core\Helper\NotificationTemplateHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeSalaryIncrementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public object $user;

    public string $template;

    public $subject;

    public function __construct($user, $updatedSalary)
    {
        $tag = new SalaryTag($user, auth()->user(), $user, $updatedSalary);

        $this->user = $user;

        $template = $this->template();

        $this->template = optional($template)->parse(
            method_exists($tag, 'notification') ? $tag->notification() : ['{name}' => optional($user)->full_name]
        );

        $this->subject = optional($template)->parseSubject(
            method_exists($tag, 'salaryIncrementSubject') ? $tag->salaryIncrementSubject() : ['{name}' => optional($user)->full_name]
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('notification.mail.user.template', [
            'template' => $this->template
        ])->subject($this->subject);
    }

    public function template()
    {
        return NotificationTemplateHelper::new()
            ->on('employee_salary_increment')
            ->mail();
    }
}
