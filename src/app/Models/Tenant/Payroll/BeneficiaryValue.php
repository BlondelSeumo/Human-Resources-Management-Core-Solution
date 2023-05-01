<?php

namespace App\Models\Tenant\Payroll;

use App\Models\Core\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeneficiaryValue extends BaseModel
{
    use HasFactory;

    protected $fillable = ['beneficiary_id', 'amount', 'is_percentage', 'beneficiary_valuable_type', 'beneficiary_valuable_id'];

    public function beneficiaryValuable()
    {
        return $this->morphTo();
    }

    public function beneficiary()
    {
        return $this->belongsTo(Beneficiary::class);
    }
}
