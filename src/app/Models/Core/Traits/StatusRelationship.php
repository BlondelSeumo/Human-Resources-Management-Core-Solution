<?php


namespace App\Models\Core\Traits;


use App\Models\Core\Status;

trait StatusRelationship
{
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'id');
    }
}
