<?php
namespace Database\Seeders\App;

use App\Models\Core\Notification\NotificationTemplate;
use App\Models\Core\Setting\NotificationEvent;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;

class TenantNotificationTemplateSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        NotificationEvent::with('type')
            ->when(!optional(tenant())->is_single, function (Builder $builder) {
                $builder->whereHas('type', function (Builder $builder) {
                    $builder->where('alias', 'tenant');
                });
            })
            ->get()
            ->each(function (NotificationEvent $event) {
                $ignore = [
                    'user_invitation',
                    'user_create',
                    'password_reset',
                    'tenantInvitation_canceled',
                    'user_invitation_canceled',
                    'employee_invitation',
                    'employee_terminated',
                    'employee_invitation_canceled',
                    'employee_salary_increment',
                    'employee_payslip_generate',
                    'employee_password_reset'
                ];

                if (!in_array($event->name, $ignore)) {
                    $workingShift = explode('_', $event->name);
                    $templates = ['system' => '', 'subject' => '', 'content' => '' ];

                    if (count($workingShift) == 3) {
                        [$name, $middle, $action] = $workingShift;
                        $name = $name.'_'.$middle;
                    }else{
                        [$name, $action] = $workingShift;
                    }

                    if (array_key_exists($event->name, $this->template())) {
                        $templates = $this->template()[$event->name];
                    }elseif (array_key_exists($action, $this->template())) {
                        $templates = $this->template()[$action];
                    }

                    $mail = $this->saveEmailTemplate($templates, $event, $name);

                    $database = $this->saveDatabaseTemplate($templates, $name);

                    $event->templates()->attach(
                        [ $database->id, $mail->id ]
                    );

                }else {
                    $templates = $this->template()[$event->name];
                    $mail = $this->saveEmailTemplate($templates, $event, null);
                    $event->templates()->attach(
                        [$mail->id ]
                    );
                }

            });
        $this->enableForeignKeys();
    }

    public function template()
    {
        return [
            'password_reset' => [
                'subject' => 'Password reset link provided by {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>Your request for reset password has been approved from {company_name}. Press the button below to reset the password.</p><br>
<p><a href="{reset_password_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">Reset password</a></p><br>

We are highly expecting you as soon as possible. Hope you\'ll join us.
<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>',
            ],
            'user_invitation' => [
                'subject' => 'User invitation form {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>Hope this mail finds you well and healthy. We are informing you that you\'ve been invited to our application by {action_by}. It\'ll be a great opportunity to work with you.</p><br>
<p><a href="{invitation_url}" target="_blank" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none">Accept Invitation</a></p><br>

<p></p><p>Thanks &amp; Regards,
</p><p>{company_name}</p>',
            ],
            'user_invitation_canceled' => [
                'system' => '',
                'subject' => 'Your invitation from {app_name} has been canceled',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p><p>Hi,</p><p>We are sorry to inform you that the invitation you have received from us has been canceled due to some reason. We hope to work with you in the future time in better condition</p><br><p>Thanks for being with us<br>{app_name}</p>',
            ],
            'user_joined' => [
                'system' => 'A new user has been joined in {company_name}',
                'subject' => 'A new user has been joined in {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>It\'s a piece of good news that a new user {name} has been joined in our application invited by {action_by}. Hope you will enjoy his work and collaborations.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">View Resource</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'user_invited' => [
                'system' => 'A new user has been invited by {action_by}',
                'subject' => 'A new user has been invited by {action_by}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>Hope this mail finds you well and healthy. We are informing you that a new user has been invited to our application by {action_by}. It\'ll be a great opportunity to work with him.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">View Resource</a></p><br>
<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'employee_invited' => [
                'system' => 'A new employee has been invited by {action_by}',
                'subject' => 'A new employee has been invited by {action_by}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>Hope this mail finds you well and healthy. We are informing you that a new employee has been invited to our application by {action_by}. It\'ll be a great opportunity to work with him.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">View Resource</a></p><br>
<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'tenantInvitation_canceled' => [
                'system' => '',
                'subject' => 'Your invitation for the {name} has been canceled',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p><p>Hi {receiver_name},</p><p>We are sorry to inform you that the invitation you have received from us has been canceled due to some reason. We hope to work with you in the future time in better condition</p><br><p>Thanks for being with us<br>{company_name}</p>',
            ],
            'tenantInvitation_activation' => [
                'system' => '',
                'subject' => 'Tenant {name} invitation activation mail',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p><p></p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>Hope this mail finds you well and healthy. We are informing you that you\'ve been invited to our application&nbsp;<span style="font-size: 13.9876px; background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">&nbsp;</span><span style="font-size: 13.9876px; background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">by {action_by}</span><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">&nbsp;to join in a new tenant. It\'ll be a great opportunity to work with you.</span></p><br><p><a href="{invitation_url}" target="_blank" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none">Accept Invitation</a></p><br><p></p><p>Thanks &amp; Regards,</p><p>{company_name}</p>',
            ],
//            'employee_terminated' => [
//                'system' => '',
//                'subject' => 'Termination notice from {company_name}',
//                'content' => '<p><img src="{company_logo}" style="height: 75px"></p><p>Hi {receiver_name},</p><p>We are sorry to inform you that you have been terminated from our company due to some reason. We are hoping for your better future.</p><br><p>Thanks for being with us<br>{company_name}</p>',
//            ],
            'employee_termination' => [
                'system' => 'An employee named {name} has been terminated by {action_by}',
                'subject' => 'An employee named {name} has been terminated by {action_by}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p><p>Hi {receiver_name},</p><p>We are sorry to inform that an employee named {name} has been terminated from {company_name} due to some reason. We are hoping for his/her better future.</p><br><p>Regards<br>{company_name}</p>',
            ],
            'employee_invitation' => [
                'system' => '',
                'subject' => 'Employee invitation',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>Hope this mail finds you well and healthy. We are informing you that you\'ve been invited as an employee to our company by {action_by}. It\'ll be a great opportunity to work with you.</p><br>
<p><a href="{invitation_url}" target="_blank" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none">Accept Invitation</a></p><br>

<p></p><p>Thanks &amp; Regards,
</p><p>{company_name}</p>'
            ],
            'employee_password_reset' => [
                'system' => '',
                'subject' => 'Employee password reset invitation',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>We are informing you that you have been invited to our application by {action_by}. We will request you to reset your password to complete the signup process and update your basic information from your profile. Press the button below to reset the password.</p><br>
<p><a href="{invitation_url}" target="_blank" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none">Reset Password</a></p><br>
<p>We are highly expecting you to join as soon as possible.</p>
<p>Thanks for being with us.</p><p>Regards,
</p><p>{company_name}</p>'
            ],
            'employee_invitation_canceled' => [
                'system' => '',
                'subject' => 'Your invitation from {app_name} has been canceled',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p><p>Hi,</p><p>We are sorry to inform you that the invitation you have received from us has been canceled due to some reason. We hope to work with you in the future time in better condition</p><br><p>Thanks for being with us<br>{app_name}</p>',
            ],
            'employee_salary_increment' => [
                'system' => '',
                'subject' => 'Confirmation Message of Salary Increment',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>Dear {receiver_name}</p>
<p>We are pleased to inform you that based on your last performance review, we have decided to award you a salary increment. Your new monthly salary will be BDT {salary_amount} which will be effective from the {effective_date}.</p>
<br><p>Congratulations and thank you for your ongoing contributions.<br><p>Regards,</p><p>{company_name}</p>',
            ],
            'salary_increment' => [
                'system' => 'An employee named {name} has awarded a Salary Increment',
                'subject' => 'Confirmation Message of Salary Increment for employee named {name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>Dear {receiver_name}</p>
<p>We are pleased to inform you that based on the last performance review, we have decided to award {employee_name} a salary increment. The new monthly salary will be BDT {salary_amount} which will be effective from the {effective_date}.</p>
<br><p>Regards,</p><p>{company_name}</p>',
            ],
            'employee_payslip_generate' => [
                'system' => '',
                'subject' => 'Payslip for {receiver_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>Dear {receiver_name}</p>
<p>Here is your Payslip</p>
<br><p>Regards,</p><p>{company_name}</p>',
            ],
            'created' => [
                'system' => 'A new {resource} named {name} has been created by {action_by}.',
                'subject' => 'A new {resource} has been created in {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>It\'s a piece of good news that a new {resource} named {name} has been created in our application by {action_by}. Please have a look at that.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; ; text-decoration: none; text-underline: none" target="_blank">{button_label}</a></p><br>
<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'updated' => [
                'system' => 'A {resource} named {name} has been updated by {action_by}.',
                'subject' => 'A {resource} has been updated in {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>It\'s a piece of good news that a {resource} named {name} has been updated in our application by {action_by}. Please have a look at that.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">{button_label}</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'deleted' => [
                'system' => 'A {resource} named {name} has been deleted by {action_by}.',
                'subject' => 'A {resource} has been deleted in {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>We are going to inform you that a {resource} named {name} has been deleted from our application by {action_by}.</p>
<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'activated' => [
                'system' => 'A {resource} named {name} has been activated by {action_by}.',
                'subject' => 'A {resource} has been activated in {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>It\'s a piece of good news that a {resource} named {name} has been activated in our application by {action_by}. Please have a look at that.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">{button_label}</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'deactivated' => [
                'system' => 'A {resource} named {name} has been deactivated by {action_by}.',
                'subject' => 'A {resource} has been deactivated in {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>We are going to inform you that a {resource} named {name} has been deactivated from our application by {action_by}.</p>
<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'confirmed' => [
                'system' => 'A {resource} named {name} has been confirmed by {action_by}.',
                'subject' => 'A {resource} has been confirmed in {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>It\'s a piece of good news that a {resource} named {name} has been confirmed in {company_name} by {action_by}. Please have a look at that.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">{button_label}</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'sent' => [
                'system' => 'A {resource} named {name} has been sent successfully by {action_by}.',
                'subject' => 'A {resource} has been sent successfully in {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>It\'s a piece of good news that a {resource} named {name} has been sent successfully in {company_name} brand by {action_by}. Please have a look at that.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">{button_label}</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'requested' => [
                'system' => 'An employee named {name} has been requested for {resource}',
                'subject' => 'An employee named {name} has been requested for {resource}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>An employee named {name} has been requested for {resource} modification. Please take a look.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">View {resource}</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'rejected' => [
                'system' => 'Your {resource} request has been rejected by authorities',
                'subject' => '{resource} request rejected',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>We are sorry to inform you that your {resource} request got rejected from HR. Please take a look at your dashboard.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">View {resource}</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'approved' => [
                'system' => 'Your {resource} request has been approved by authorities',
                'subject' => '{resource} request approved',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>We are thrilled to inform you that your {resource} request got approved from HR. Please take a look at your dashboard.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">View {resource}</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'canceled' => [
                'system' => 'Your {resource} has been canceled by authorities',
                'subject' => '{resource} canceled',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>We are sorry to inform you that your {resource} request got canceled from HR. Please take a look at your dashboard.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">View {resource}</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'bypassed' => [
                'system' => 'A {resource} has been bypassed to you by {name}',
                'subject' => '{resource} bypassed',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>A {resource} bypassed to you by {name}. Please take a look.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">View {resource}</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'assigned' => [
                'system' => 'Your {resource} has been assigned by manager',
                'subject' => '{resource} assigned',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>Your {resource} assigned by manager. Please take a look.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">View {resource}</a></p><br>

<p></p><p>Thanks for being with us.
</p><p>Regards,</p><p>{company_name}</p><p></p><p></p>'
            ],
            'user_create' => [
                'system' => '',
                'subject' => 'Account has been created form {company_name}',
                'content' => '<p><img src="{company_logo}" style="height: 75px"></p>
<p>
</p><p><span style="background-color: var(--form-control-bg) ; color: var(--default-font-color) ;">Hi {receiver_name}</span><br></p><p>Hope this mail finds you well and healthy. You have been added to our company as an employee.
<p>Your Login credentials are below, </p> 
<p>Email : {email} </p> 
<p>Password : {password}</p>
<br>
<p>Please use these credentials to login into your account.</p><br>
<p><a href="{resource_url}" style="background: #4466F2;color: white;padding: 9px;border-radius: 4px;cursor: pointer; text-decoration: none; text-underline: none" target="_blank">Go to your account</a></p><br>
<p>You can change your password from your account password settings.</p>
Hope you will find useful!
<p></p><p>Thanks &amp; Regards,
</p><p>{company_name}</p>',
            ],
        ];
    }

    public function saveEmailTemplate($templates, $event, $name = null)
    {
        return NotificationTemplate::query()->create([
            'subject' => strtr($templates['subject'], [
                '{resource}' => __t($name),
                '{company_name}' => $event->type->alias == 'app' ? '{company_name}' : '{company_name}'
            ]),
            'default_content' => strtr($templates['content'],[
                '{resource}' => __t($name),
                '{button_label}' => 'View '.ucfirst(__t($name))
            ]),
            'custom_content' => null,
            'type' => 'mail'
        ]);
    }

    public function saveDatabaseTemplate($templates, $name)
    {
        return NotificationTemplate::create([
            'subject' => null,
            'default_content' => strtr($templates['system'], [
                '{resource}' => __t($name),
            ]),
            'custom_content' => null,
            'type' => 'database'
        ]);
    }
}
