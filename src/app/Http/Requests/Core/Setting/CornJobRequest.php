<?php

namespace App\Http\Requests\Core\Setting;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Setting\Setting;

class CornJobRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return (new Setting())->cornJobRules();
    }
}
