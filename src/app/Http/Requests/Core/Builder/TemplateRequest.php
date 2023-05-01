<?php

namespace App\Http\Requests\Core\Builder;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Builder\Template\Template;

class TemplateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \App\Exceptions\GeneralException
     */
    public function rules()
    {
        return $this->initRules(new Template());
    }
}
