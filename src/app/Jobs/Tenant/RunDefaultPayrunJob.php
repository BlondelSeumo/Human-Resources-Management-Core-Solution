<?php

namespace App\Jobs\Tenant;

use App\Models\Core\Auth\User;
use App\Services\Tenant\Payroll\PayrunService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunDefaultPayrunJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $payrun;
    public User $user;
    public array $settings;
    public array $beneficiaries;
    public array $ranges;

    public function __construct($payrun, User $user, $settings, $beneficiaries, $ranges = [])
    {
        $this->payrun = $payrun;
        $this->user = $user;
        $this->settings = $settings;
        $this->beneficiaries = $beneficiaries;
        $this->ranges = $ranges;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        resolve(PayrunService::class)
            ->generatePayslip(
                $this->payrun,
                $this->user,
                $this->settings,
                $this->beneficiaries,
                $this->ranges
            );
    }
}
