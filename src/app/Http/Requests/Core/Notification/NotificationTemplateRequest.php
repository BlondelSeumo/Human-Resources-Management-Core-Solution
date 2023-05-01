<?php

namespace App\Http\Requests\Core\Notification;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Notification\NotificationTemplate;

class NotificationTemplateRequest extends BaseRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \App\Exceptions\GeneralException
     */
    public function rules()
    {
        return $this->initRules(new NotificationTemplate());
    }
}
