<?php


namespace App\Helpers\Traits;


use App\Models\Tenant\Payroll\Payslip;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait ConflictedPayslipQueryHelpers
{
    public function conflictedPayrunsPayslip($payrunId): Collection
    {
        return DB::table('payslips')
            ->where('payslips.payrun_id', '=', $payrunId)
            ->join('users', 'payslips.user_id', 'users.id')
            ->join('payslips as conflict', function (JoinClause $join){
                $this->conflictLogic($join);
            })
            ->select('payslips.user_id')
            ->distinct()
            ->get();
    }

    public function conflictLogic($join)
    {
        $join->on('payslips.user_id', 'conflict.user_id')
            ->on('payslips.id', '!=', 'conflict.id')
            ->on(function ($q){
                $q->orOn(function ($q) {
                    $q->on(DB::raw('DATE(conflict.start_date)'), '>=', DB::raw('DATE(payslips.start_date)'))
                        ->on(DB::raw('DATE(conflict.start_date)'), '<=', DB::raw('DATE(payslips.end_date)'));
                })->orOn(function ($q) {
                    $q->on(DB::raw('DATE(conflict.end_date)'), '>=', DB::raw('DATE(payslips.start_date)'))
                        ->on(DB::raw('DATE(conflict.end_date)'), '<=', DB::raw('DATE(payslips.end_date)'));
                })->orOn(function ($query) {
                    $query->on(DB::raw('DATE(conflict.start_date)'), '>=', DB::raw('DATE(payslips.start_date)'))
                        ->on(DB::raw('DATE(conflict.end_date)'), '<=', DB::raw('DATE(payslips.end_date)'));
                })->orOn(function ($query) {
                    $query->on(DB::raw('DATE(conflict.start_date)'), '<=', DB::raw('DATE(payslips.start_date)'))
                        ->on(DB::raw('DATE(conflict.end_date)'), '>=', DB::raw('DATE(payslips.end_date)'));
                });
            });
    }

    public function getConflictPayslipsByPayrunUser($payrunId, $userId): Collection
    {
        return DB::table('payslips')
            ->where('payslips.payrun_id', '=', $payrunId)
            ->where('payslips.user_id', $userId)
            ->join('payslips as conflict', function (JoinClause $join){
                $this->conflictLogic($join);
            })
            ->select(
                'conflict.id as conflicted_id',
                'payslips.id as payslip_id',
            )
            ->get();
    }

    public function conflictedUserPayslip(Payslip $payslip, $start_date, $end_Date): Collection
    {
        return Payslip::query()
            ->where('user_id', $payslip->user_id)
            ->where('id', '!=', $payslip->id)
            ->where(function ($q) use($payslip){
                $q->orWhere(function ($q) use($payslip) {
                    $q->where(DB::raw('DATE(start_date)'), '>=', $payslip->start_date)
                        ->where(DB::raw('DATE(start_date)'), '<=', $payslip->end_date);
                })->orWhere(function ($q) use($payslip){
                    $q->where(DB::raw('DATE(end_date)'), '>=', $payslip->start_date)
                        ->where(DB::raw('DATE(end_date)'), '<=', $payslip->end_date);
                })->orWhere(function ($query) use($payslip){
                    $query->where(DB::raw('DATE(start_date)'), '>=', $payslip->start_date)
                        ->where(DB::raw('DATE(end_date)'), '<=', $payslip->end_date);
                })->orWhere(function ($query) use($payslip){
                    $query->where(DB::raw('DATE(start_date)'), '<=', $payslip->start_date)
                        ->where(DB::raw('DATE(end_date)'), '>=', $payslip->end_date);
                });
            })
            ->get();
    }

    public function getConflictedPayrun(): array
    {
        return DB::table('payslips')
            ->join('payruns', 'payslips.payrun_id', '=', 'payruns.id')
            ->join('users', 'payslips.user_id', 'users.id')
            ->join('payslips as conflict', function (JoinClause $join){
                $this->conflictLogic($join);
            })->select('payslips.payrun_id')
            ->distinct()
            ->orderBy('payrun_id', 'DESC')
            ->get()
            ->pluck('payrun_id')
            ->toArray();
    }

    public function getConflictedPayslip(): array
    {
        return DB::table('payslips')
            ->join('payruns', 'payslips.payrun_id', '=', 'payruns.id')
            ->join('users', 'payslips.user_id', 'users.id')
            ->join('payslips as conflict', function (JoinClause $join){
                $this->conflictLogic($join);
            })->select('payslips.id')
            ->distinct()
            ->orderBy('id', 'DESC')
            ->get()
            ->pluck('id')
            ->toArray();
    }
}