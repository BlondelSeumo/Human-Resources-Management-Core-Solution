<?php

namespace App\Models\Tenant\Employee\Announcement;

use App\Helpers\Traits\DateTimeHelper;
use App\Models\Core\Traits\BootTrait;
use App\Models\Tenant\Employee\Announcement\Relationship\AnnouncementRelationShip;
use App\Models\Tenant\Employee\Announcement\Rules\AnnouncementRules;
use App\Models\Tenant\TenantModel;

class Announcement extends TenantModel
{
    use AnnouncementRelationShip, BootTrait, DateTimeHelper, AnnouncementRules;

    protected $fillable = [
        'name', 'start_date', 'end_date', 'description', 'tenant_id', 'created_by'
    ];

    public function setStartDateAttribute($date = null)
    {
        if ($date) {
            $this->attributes['start_date'] = $this->carbon($date)->parse()->format('Y-m-d');
        } else {
            $this->attributes['start_date'] = $date;
        }
    }

    public function setEndDateAttribute($date = null)
    {
        if ($date) {
            $this->attributes['end_date'] = $this->carbon($date)->parse()->format('Y-m-d');
        } else {
            $this->attributes['end_date'] = $date;
        }
    }
}
