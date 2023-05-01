<?php

namespace App\Models\Tenant\Employee;

use App\Models\Core\Auth\User;
use App\Models\Core\BaseModel;
use App\Models\Core\Traits\CreatedByRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends BaseModel
{
    use HasFactory, CreatedByRelationship;

    protected $fillable = ['created_by', 'name', 'path', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
