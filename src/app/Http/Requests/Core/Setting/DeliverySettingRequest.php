<?php

namespace App\Http\Requests\Core\Setting;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Traits\MailRules;
use Illuminate\Support\Str;

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
        $method = Str::camel($this->provider).'Rules';

        if ($method !== 'Rules' && method_exists($this, $method)) {
            return $this->{$method}();
        } else {
            return [
                'provider' => 'required|in:'.implode(',', array_keys(config('settings.supported_mail_services')))
            ];
        }
    }
}
