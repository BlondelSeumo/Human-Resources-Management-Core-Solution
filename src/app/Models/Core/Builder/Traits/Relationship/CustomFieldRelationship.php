<?php


namespace App\Models\Core\Builder\Traits\Relationship;


use App\Models\Core\Builder\Form\CustomFieldType;
use App\Models\Core\Builder\Form\CustomFieldValue;
use App\Models\Core\Traits\CreatedByRelationship;

trait CustomFieldRelationship
{
    use CreatedByRelationship;

    public function customFieldValues()
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    public function customFieldType()
    {
        return $this->belongsTo(CustomFieldType::class);
    }

}
