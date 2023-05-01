<?php

namespace App\Models\Tenant\Payroll;

use App\Models\Core\Traits\StatusRelationship;
use App\Models\Tenant\TenantModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Payrun extends TenantModel
{
    use HasFactory, StatusRelationship;

    protected $fillable = ['name', 'data', 'status_id', 'uid', 'followed', 'batch_id'];

    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }

    public function beneficiaries()
    {
        return $this->morphMany(BeneficiaryValue::class, 'beneficiary_valuable');
    }

    public static function boot()
    {
        parent::boot();

        static::creating( function ($model){
            return $model->fill([
                'uid' => self::uniqueId()
            ]);
        });

        static::deleting(function($model) {
            $model->beneficiaries()->delete();
            $model->payslips()->get()->map(function ($item){
                $item->beneficiaries()->delete();
            });
            $model->payslips()->delete();
        });
    }

    public static function uniqueId($length = 8): string
    {
        $uniqueId = strtoupper(Str::random($length));

        if(self::query()->where('uid', $uniqueId)->exists()){
            self::uniqueId();
        }

        return $uniqueId;
    }
}
