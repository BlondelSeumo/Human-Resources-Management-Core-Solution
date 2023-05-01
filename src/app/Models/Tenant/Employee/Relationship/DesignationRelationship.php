<?php


namespace App\Models\Tenant\Employee\Relationship;


use App\Models\Core\Auth\User;
use App\Models\Tenant\Employee\DesignationUser;

trait DesignationRelationship
{
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'designation_user',
            'designation_id',
            'user_id'
        )->using(DesignationUser::class)
            ->withPivot('start_date', 'end_date');
    }
}
