<?php

namespace App\Models\Tenant\Payroll;

use App\Models\Core\Auth\User;
use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\This;

class Payslip extends TenantModel
{
    use HasFactory, StatusRelationship;

    protected $fillable = ['user_id','status_id',
        'start_date','period','end_date','net_salary', 'consider_type',
        'consider_overtime','payrun_id', 'basic_salary'];

    public function beneficiaries()
    {
        return $this->morphMany(BeneficiaryValue::class, 'beneficiary_valuable');
    }

    public function payrun()
    {
        return $this->belongsTo(Payrun::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function boot()
    {
        parent::boot();

        static::deleting(function($model) {
            $model->beneficiaries()->delete();
        });
    }
}
