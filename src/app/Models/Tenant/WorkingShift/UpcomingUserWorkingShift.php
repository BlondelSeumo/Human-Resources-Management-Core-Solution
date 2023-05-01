<?php

namespace App\Models\Tenant\WorkingShift;

use App\Models\Core\Auth\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpcomingUserWorkingShift extends Model
{
    protected $fillable = ['user_id', 'working_shift_id', 'start_date'];

    use HasFactory;

    public function user()
    {
        $this->belongsTo(User::class);
    }

    public function workingShift()
    {
        return $this->belongsTo(WorkingShift::class);
    }
}
