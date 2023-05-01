<?php


namespace App\Models\Core\Traits;


use App\Models\Core\Builder\Form\CustomFieldValue;

trait CustomFieldRelationship
{
    public function customFields()
    {
        return $this->morphMany(CustomFieldValue::class, 'contextable');
    }
}
