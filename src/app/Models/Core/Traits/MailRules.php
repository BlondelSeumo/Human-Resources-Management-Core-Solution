<?php


namespace App\Models\Core\Traits;


trait MailRules
{

    protected $basicRules = [
        'from_email' => 'required|email',
        'from_name' => 'required|min:3',
    ];

    public function mailgunRules()
    {
        return array_merge([
            'domain_name' => 'required|min:3',
            'api_key' => 'required|min:3'
        ], $this->basicRules);
    }

    public function amazonSesRules()
    {
        return array_merge([
            'access_key_id' => 'required|min:3',
            'secret_access_key' => 'required|min:3',
            'api_region' => 'required',
        ], $this->basicRules);
    }

    public function smtpRules()
    {
        return array_merge([
            'smtp_host' => 'required|min:5',
            'smtp_port' => 'required|min:2',
            'smtp_encryption' => 'required',
            'smtp_user_name' => 'required|min:2',
            'smtp_password' => 'required|min:2',
        ], $this->basicRules);
    }

    public function mandrillRules()
    {
        return array_merge([
            'mandrill_secret' => 'required|min:5'
        ], $this->basicRules);
    }

    public function postmarkRules()
    {
        return array_merge([
            'postmark_token' => 'required|min:3'
        ], $this->basicRules);
    }

    public function sparkpostRules()
    {
        return array_merge([
            'sparkpost_secret' => 'required|min:3'
        ], $this->basicRules);
    }

    public function sendmailRules()
    {
        return array_merge([
            'path' => 'nullable|min:3'
        ], $this->basicRules);
    }

}
