<?php


namespace App\Models\Tenant\Salary\Relationship;


use App\Models\Core\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait SalaryRelationship
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
}