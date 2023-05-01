<?php


namespace App\Mail\Tag;


class SalaryTag extends Tag
{
    use \App\Notifications\Traits\Tag;

    protected $user;

    protected $salary;

    public function __construct($user, $notifier = null, $receiver = null, $salary = null)
    {
        $this->user = $user;
        $this->notifier = $notifier;
        $this->receiver = $receiver;
        $this->salary = $salary;
    }

    public function salaryIncrementSubject(): array
    {
        return $this->commonForSubject();
    }

    function notification()
    {
        return array_merge([
            '{employee_name}' => optional($this->user)->full_name,
            '{salary_amount}' => optional($this->salary)->amount,
            '{effective_date}' => optional($this->salary)->start_at
        ], $this->common());
    }
}