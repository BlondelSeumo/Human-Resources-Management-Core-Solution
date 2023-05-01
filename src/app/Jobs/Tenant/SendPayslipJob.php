<?php

namespace App\Jobs\Tenant;

use App\Models\Tenant\Payroll\Payslip;
use App\Services\Tenant\Payroll\PayslipService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPayslipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Payslip $payslip;
    private PayslipService $service;

    public function __construct($payslip)
    {
        $this->payslip = $payslip;
    }


    public function handle(PayslipService $service)
    {
        $this->service = $service;
        $this->payslip->load($this->service->getRelations());
        $this->service
            ->setModel($this->payslip)
            ->setAttribute('file_path', 'public/payslip/payslip_' . $this->payslip->id . '.pdf')
            ->generateAndSavePayslip()
            ->sendPayslipToUser()
            ->updatePayslipStatus()
            ->updatePayrunStatus();
    }
}
