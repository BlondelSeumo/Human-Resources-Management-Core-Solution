<?php

namespace Gainhq\Installer\App\Request;


use Gainhq\Installer\App\Models\Core\Traits\MailRules;

class DeliverySettingRequest extends BaseRequest
{
    use MailRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->provider == 'mailgun')
            return $this->mailgunRules();
        elseif ($this->provider == 'amazon_ses')
            return $this->amazonSesRules();
        else
            return [ 'provider' => 'required' ];
    }
}
