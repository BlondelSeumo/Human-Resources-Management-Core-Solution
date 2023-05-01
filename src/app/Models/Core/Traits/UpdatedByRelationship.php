<?php


namespace App\Models\Core\Traits;


use App\Models\Core\Auth\User;

trait UpdatedByRelationship
{
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
