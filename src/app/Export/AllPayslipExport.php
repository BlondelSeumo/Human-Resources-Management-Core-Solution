<?php


namespace App\Export;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllPayslipExport implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable, DateTimeHelper, DateRangeHelper;


    private Collection $payslips;

    public function __construct(Collection $payslips)
    {
        $this->payslips = $payslips;
    }

    public function headings(): array
    {
        return [
            __t('employee'),
            __t('department'),
            __t('payrun_date'),
            __t('payrun_id'),
            __t('payrun_period'),
            __t('payrun_type'),
            __t('salary'),
            __t('net_salary'),
        ];
    }

    public function array(): array
    {
        return $this->payslips->map(function ($payslip) {
            return [
                $payslip->user->full_name??'-',
                $payslip->user->department->name??'-',
                $this->getDateDifferenceString($payslip->start_date, $payslip->end_date),
                $payslip->payrun->uid??'-',
                $payslip->period ? ucwords(__t($payslip->period)):'-',
                $this->getPayrunType($payslip->payrun),
                $payslip->basic_salary ? $payslip->basic_salary:0,
                $payslip->net_salary??0
            ];
        })->toArray();
    }

    private function getDateDifferenceString($start, $end)
    {
        $startMonthNo = date('m',strtotime($start));
        $endMonthNo = date('m',strtotime($end));

        $startFormat = date('d',strtotime($start));
        $endFormat = date('d M Y',strtotime($end));
        if ($startMonthNo !== $endMonthNo) {
            $startFormat = date('d M',strtotime($start));
        }

        return $startFormat. '-'. $endFormat;
    }
    
    private function getPayrunType($payrun)
    {
        $data = json_decode($payrun->data);
        $type = '-';
        if(isset($data->type) && $data->type){
            $type = ucwords($data->type);
        }
        return $type;
    }
}