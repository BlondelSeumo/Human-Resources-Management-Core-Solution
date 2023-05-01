<?php


namespace App\Models\Core\Traits;


use App\Models\Core\Auth\Type;

trait TypeRelationship
{
    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
