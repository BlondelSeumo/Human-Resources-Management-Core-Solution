<?php

namespace App\Models\Core\Builder\Form;

use App\Models\Core\BaseModel;
use App\Models\Core\Traits\Translate\TranslatedNameTrait;


class CustomFieldType extends BaseModel
{
    protected $appends = ['translated_name'];

    use TranslatedNameTrait;

    protected $fillable = [
        'name'
    ];

}
