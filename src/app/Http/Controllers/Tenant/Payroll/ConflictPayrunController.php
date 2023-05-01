<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Helpers\Traits\ConflictedPayslipQueryHelpers;
use App\Http\Controllers\Controller;
use App\Models\Core\Auth\User;
use App\Models\Tenant\Payroll\Payrun;
use App\Models\Tenant\Payroll\Payslip;
use Illuminate\Database\Eloquent\Builder;

class ConflictPayrunController extends Controller
{
    use ConflictedPayslipQueryHelpers;

    private array $payslipsId = [];

    public function users(Payrun $payrun)
    {
        $usersId = $this->conflictedPayrunsPayslip($payrun->id)->pluck('user_id')->toArray();
        return User::query()->with([
            'profilePicture',
            'status',
        ])->whereIn('id', $usersId)->get();
    }

    public function userPayslips(Payrun $payrun, User $user)
    {
        $payslips = $this->getConflictPayslipsByPayrunUser($payrun->id, $user->id);
        $payslips->map(function ($item){
            array_push($this->payslipsId, $item->conflicted_id);
            array_push($this->payslipsId, $item->payslip_id);
        });

        return Payslip::query()
            ->with('payrun')
            ->whereIn('id', array_unique($this->payslipsId))
            ->orWhere(fn (Builder $builder) =>
                $builder->where('payrun_id', $payrun->id)
                    ->where('user_id', $user->id)
            )->get();
    }
}
