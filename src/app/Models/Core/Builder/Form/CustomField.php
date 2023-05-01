<?php

namespace App\Models\Core\Builder\Form;

use App\Models\Core\BaseModel;
use App\Models\Core\Builder\Traits\Relationship\CustomFieldRelationship;
use App\Models\Core\Traits\BootTrait;
use App\Models\Core\Traits\DescriptionGeneratorTrait;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Traits\LogsActivity;

class CustomField extends BaseModel
{
    use CustomFieldRelationship, BootTrait, DescriptionGeneratorTrait;

    protected static $logAttributes = [
        'name', 'context', 'meta', 'customFieldType.name'
    ];

    protected $fillable = [
        'name', 'context', 'meta', 'in_list', 'is_for_admin', 'custom_field_type_id', 'created_by'
    ];

    public function createdRules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'custom_field_type_id' => 'required|integer|not_in:0',
            'meta' => [Rule::requiredIf(function () {
                return in_array(request()->get('type'), ['radio', 'select']);
            })],
            'context' => 'required'
        ];
    }

}
