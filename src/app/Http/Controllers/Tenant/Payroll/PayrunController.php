<?php

namespace App\Http\Controllers\Tenant\Payroll;

use App\Filters\Tenant\PayrunFilter;
use App\Helpers\Traits\ConflictedPayslipQueryHelpers;
use App\Http\Controllers\Controller;
use App\Jobs\Tenant\SendPayslipJob;
use App\Models\Tenant\Payroll\Payrun;
use App\Models\Tenant\Payroll\Payslip;
use App\Repositories\Core\Status\StatusRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrunController extends Controller
{
    use ConflictedPayslipQueryHelpers;

    public function __construct(PayrunFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index(Request $request)
    {
        $collections = Payrun::filters($this->filter)
            ->select('id', 'name', 'uid', 'status_id', 'data', 'followed', 'created_at', 'batch_id')
            ->withCount(['payslips as sent_payslip' => fn($q) =>
                $q->where('status_id', resolve(StatusRepository::class)->payslipSent()),
            ])->withCount(['payslips'])
            ->with([
                'status:id,name,class',
            ])->when($request->has('conflicted') && $request->get('conflicted') == 'true', function (Builder $builder){
                $builder->whereIn('id', $this->getConflictedPayrun());
            })->latest()
            ->paginate($request->get('per_page', 10));

        $collections->map(function ($payrun){
           $conflictedData = $this->conflictedPayrunsPayslip($payrun->id);
            $payrun->conflicted = $conflictedData->count();
        });

        return $collections;
    }

    public function delete(Payrun $payrun)
    {
        $payrun->delete();

        return deleted_responses('payrun');
    }

    public function sendPayslips(Payrun $payrun)
    {
        $payrun->payslips->each(fn(Payslip $payslip) => SendPayslipJob::dispatch($payslip));

        $statusPending = resolve(StatusRepository::class)->payslipPending();
        $payrun->payslips()->update(['status_id' => $statusPending]);

        $statusPartially = resolve(StatusRepository::class)->payrunPartially();
        $payrun->update(['status_id' => $statusPartially]);

        return response()->json(['status' => true, 'message' => trans('default.payslip_has_started_sending')]);
    }

    public function updateBatch(Payrun $payrun)
    {
        return $payrun->update(['batch_id' => null]);
    }
}
