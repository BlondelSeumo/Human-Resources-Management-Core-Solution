<?php


namespace Gainhq\Installer\App\Models\Core\Traits;

use Gainhq\Installer\App\Models\Core\User;

trait CreatedByRelationship
{
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
