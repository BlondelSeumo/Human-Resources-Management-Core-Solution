<?php

namespace App\Models\Core\Builder\Form;

use App\Models\Core\Builder\Traits\Relationship\CustomFieldValueRelationship;
use Illuminate\Database\Eloquent\Model;


class CustomFieldValue extends Model
{
    use CustomFieldValueRelationship;

    protected $fillable = [
        'value', 'contextable_type', 'contextable_id', 'custom_field_id', 'updated_by',
    ];


}
