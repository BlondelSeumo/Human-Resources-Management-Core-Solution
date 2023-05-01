<?php

namespace App\Http\Requests\Core\Builder\Form;

use App\Http\Requests\BaseRequest;
use App\Models\Core\Builder\Form\CustomField;

class CustomFieldRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \App\Exceptions\GeneralException
     */
    public function rules()
    {
        return $this->initRules(new CustomField());
    }
}
