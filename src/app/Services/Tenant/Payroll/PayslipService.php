<?php

namespace App\Services\Tenant\Payroll;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\SettingKeyHelper;
use App\Helpers\Traits\TenantAble;
use App\Mail\Tenant\EmployeePayslipMail;
use App\Models\Tenant\Payroll\Payslip;
use App\Repositories\Core\Setting\SettingRepository;
use App\Repositories\Core\Status\StatusRepository;
use App\Services\Tenant\TenantService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PayslipService extends TenantService
{
    use SettingKeyHelper, TenantAble, DateRangeHelper;

    public function __construct(Payslip $payslip)
    {
        $this->model = $payslip;
    }

    public function beneficiariesValidation(): self
    {
        validator($this->getAttributes(), [
            'allowancePercentages' => 'array|min:' . count($this->getAttr('allowances')),
            'allowanceValues' => 'array|min:' . count($this->getAttr('allowances')),
            'deductionPercentages' => 'array|min:' . count($this->getAttr('deductions')),
            'deductionValues' => 'array|min:' . count($this->getAttr('deductions')),
            'deductionValues.*' => 'required',
            'allowanceValues.*' => 'required',
        ], [
            'allowanceValues.min' => 'All allowance value field is required',
            'deductionValues.min' => 'All deduction value field is required',
            'allowanceValues.*.required' => 'All allowance value field is required',
            'deductionValues.*.required' => 'All deduction value field is required',
        ])->validate();

        return $this;
    }

    public function updateBeneficiaries($relation = 'beneficiaries'): self
    {
        $this->model->$relation()->delete();

        $allowances = $this->getAttr('allowances', []);
        $allowanceValues = $this->getAttr('allowanceValues');
        $allowancePercentages = $this->getAttr('allowancePercentages');
        foreach ($allowances as $key => $allowance) {
            $this->model->$relation()->create([
                'amount' => $allowanceValues[$key],
                'beneficiary_id' => $allowance,
                'is_percentage' => $allowancePercentages[$key],
            ]);
        }

        $deductions = $this->getAttr('deductions', []);
        $deductionValues = $this->getAttr('deductionValues');
        $deductionPercentages = $this->getAttr('deductionPercentages');
        foreach ($deductions as $key => $deduction) {
            $this->model->$relation()->create([
                'amount' => $deductionValues[$key],
                'beneficiary_id' => $deduction,
                'is_percentage' => $deductionPercentages[$key],
            ]);
        }

        return $this;
    }

    public function generateAndSavePayslip(): PayslipService
    {
        $beneficiaries = count($this->model->beneficiaries) ? $this->model->beneficiaries : ($this->model->without_beneficiary ? [] :$this->model->payrun->beneficiaries);
        $salaryAmount = $this->model->basic_salary;
        $totalAllowance = $this->getTotalBeneficiary($beneficiaries, $salaryAmount, 'allowance');
        $totalDeduction = $this->getTotalBeneficiary($beneficiaries, $salaryAmount, 'deduction');
        $payslipFor = $this->getDateDifferenceString($this->model->start_date, $this->model->end_date);
        [$setting_able_id, $setting_able_type] = $this->tenantAble();
        $settings = (object)resolve(SettingRepository::class)
            ->getFormattedSettings('tenant', $setting_able_type, $setting_able_id);
        $payslip = $this->model;
        $payslip_settings = json_decode($payslip->payrun->data);
        //if payslip pdf style not found
//        PDF::setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
//            ->setHttpContext(stream_context_create([
//                'ssl' => [
//                    'verify_peer' => false,
//                    'verify_peer_name' => false,
//                    'allow_self_signed' => true,
//                ]
//            ]))->loadView();
        $pdf = PDF::loadView('tenant.payroll.pdf.payslip',
            compact(
                'payslip',
                'beneficiaries',
                'totalAllowance',
                'totalDeduction',
                'settings',
                'salaryAmount',
                'payslipFor',
                'payslip_settings'
            )
        );
        $output = $pdf->output();
        $filePath = $this->getAttribute('file_path');
        Storage::put($filePath, $output);

        return $this;
    }

    public function sendPayslipToUser(): PayslipService
    {
        $storagePath = storage_path('app/' . $this->getAttribute('file_path'));
        try {
            Mail::to($this->model->user->email)
                ->send(new EmployeePayslipMail($this->model->user, $storagePath));
        } catch (\Exception $exception) { /* Ignore */
        }

        Storage::delete($this->getAttribute('file_path'));

        return $this;
    }

    public function updatePayslipStatus(): PayslipService
    {
        $statusSent = resolve(StatusRepository::class)->payslipSent();
        $this->model->update(['status_id' => $statusSent]);

        return $this;
    }

    public function updatePayrunStatus(): PayslipService
    {
        $partially = resolve(StatusRepository::class)->payrunPartially();
        [$generated, $pending ] = resolve(StatusRepository::class)->payslipGeneratedPending();

        if ($this->model->payrun->payslips()->whereIn('status_id', [$generated, $pending])->exists()){
            $this->model->payrun()->update(['status_id' => $partially]);
            return $this;
        }

        $sent = resolve(StatusRepository::class)->payrunSent();
        $this->model->payrun()->update(['status_id' => $sent]);

        return $this;
    }

    public function getTotalBeneficiary($beneficiaries, $salaryAmount, $type)
    {
        if (count($beneficiaries) == 0) {
            return 0;
        }

        $allowance = $beneficiaries->reduce(function ($sum, $beneficiary) use ($salaryAmount, $type) {
            if ($beneficiary->beneficiary->type == $type) {
                if ($beneficiary->is_percentage == 1) {
                    return $sum + ($salaryAmount / 100) * $beneficiary->amount;
                }
                return $sum + $beneficiary->amount;
            }
            return $sum;
        }, 0);

        return $allowance;
    }

    public function getRelations()
    {
        return [
            'payrun',
            'payrun.beneficiaries',
            'payrun.beneficiaries.beneficiary',
            'status',
            'beneficiaries',
            'beneficiaries.beneficiary',
            'user:id,first_name,last_name,email,status_id',
            'user.department:id,name',
            'user.profile',
            'user.profilePicture',
            'user.status:id,name,class',
            'user.designation:id,name',
        ];
    }

}
