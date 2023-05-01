<?php

namespace App\Models\Tenant\Employee\Announcement\Relationship;

use App\Models\Tenant\Employee\Department;
use App\Models\Core\Traits\CreatedByRelationship;

trait AnnouncementRelationShip
{
    use CreatedByRelationship;

    public function departments()
    {
        return $this->belongsToMany(
            Department::class,
            'announcement_department',
            'announcement_id',
            'department_id',
        );
    }
}
