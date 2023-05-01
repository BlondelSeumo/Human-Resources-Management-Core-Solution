<?php


namespace App\Models\Core\Traits;


use App\Models\Core\Auth\User;

trait CreatedByRelationship
{
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
