<?php


namespace App\Models\Core\Builder\Traits\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Core\Builder\Form\CustomField;
use App\Models\Core\Traits\UpdatedByRelationship;

trait CustomFieldValueRelationship
{
    use UpdatedByRelationship;

    public function  contextable()
    {
        return $this->morphTo();
    }

    public function  customField()
    {
        return $this->belongsTo(CustomField::class);
    }
}
