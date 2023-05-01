<?php


namespace App\Services\Core\Builder\Form;


use App\Models\Core\Builder\Form\CustomField;
use App\Services\Core\BaseService;

class CustomFieldService extends BaseService
{
    public function __construct(CustomField $field)
    {
        $this->model = $field;
    }

}
