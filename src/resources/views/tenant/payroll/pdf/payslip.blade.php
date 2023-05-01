<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payslip</title>
    <link rel="stylesheet" href="{{ url('css/payslip.css') }}">
</head>
<body>
<div>
    <div class="mb-5">
        <div class="text-center">
            <img src="{{ property_exists($payslip_settings, 'logo') && $payslip_settings->logo
                        ? asset($payslip_settings->logo)
                        : (property_exists($settings, 'tenant_logo') ? asset($settings->tenant_logo) : asset('images/logo/default-logo.png')) }}"
                 alt="logo"
                 class="img-fluid mb-2"
                 style="max-height: 100px; max-width: 150px;"
            />
            <h5 class="font-weight-bold">{{ property_exists($payslip_settings, 'title') && $payslip_settings->title ? $payslip_settings->title : $settings->tenant_name }}</h5>
            @if(property_exists($payslip_settings, 'address') && $payslip_settings->address)
                <p class="mb-0">
                    {{ $payslip_settings->address }}
                </p>
            @else
                <p class="mb-0">
                    {{ property_exists($settings, 'address') ? $settings->address.', ' : '' }}
                    {{ property_exists($settings, 'area') ? $settings->area.', ' : '' }}
                    {{ property_exists($settings, 'city') ? $settings->city : '' }} <br/>
                    {{ property_exists($settings, 'zip_code') ? 'ZIP-code:'.$settings->zip_code.', ' : '' }}
                    {{ property_exists($settings, 'country') ? $settings->country : '' }}
                </p>
            @endif
        </div>
    </div>
    <div class="custom-row employee-info">
        <div class="column">
            <div class="mt-2">
                <img src="{{ $payslip->user->profilePicture ? asset($payslip->user->profilePicture->path) : asset('images/avatar.png') }}"
                     class="rounded-circle"
                     width="40"
                     height="40"
                     alt="">
                <div class="d-inline-block ml-1">
                    <p class="small text-primary" style="margin-bottom: -7px">{{ $payslip->user->full_name }}</p>
                    <small class="text-muted">{{ $payslip->user->email }}</small>
                </div>
            </div>
        </div>
        <div class="column">
            <div class="pl-5">
                <p class="small mb-0">
                    <span class="text-muted">{{ __t('payslip_for') }}:</span> <span
                            class="text-info">{{ $payslipFor }}</span>
                </p>
                <p class="small mb-0">
                    <span class="text-muted">{{ __t('created_at') }}:</span> {{ $payslip->created_at->format('d M,Y') }}
                </p>
            </div>
        </div>
        <div class="column">
            <div class="float-right">
                <p class="small mb-0">
                    <span class="text-muted">{{ __t('designation') }}:</span> {{ $payslip->user->designation ? $payslip->user->designation->name : 'None' }}
                </p>
                <p class="small mb-0">
                    <span class="text-muted">{{ __t('department') }}:</span> {{ $payslip->user->department ? $payslip->user->department->name : 'None'}}
                </p>
            </div>
        </div>
    </div>
    <div class="custom-row basic-salary-info border-bottom">
        <div class="column">
            <p class="mb-1">{{ __t('basic_salary') }}</p>
        </div>
        <div class="column text-right">
            <p class="mb-1 currency-symbol">
                {{ trans('default.like_'.$settings->currency_position, [
                    'symbol' => $settings->currency_symbol,
                     'amount' => $salaryAmount
                    ]) }}
            </p>
        </div>
    </div>
    @if($payslip->consider_type != 'none')
        @if($payslip->net_salary - ($totalAllowance - $totalDeduction) > $salaryAmount)
            <div class="custom-row basic-salary-info border-bottom mt-3">
                <div class="column">
                    <p class="mb-1">{{ __t('overtime_earning') }}</p>
                </div>
                <div class="column text-right">
                    <p class="mb-1 currency-symbol">
                        {{ trans('default.like_'.$settings->currency_position, [
                            'symbol' => $settings->currency_symbol,
                             'amount' => round(($payslip->net_salary - ($totalAllowance - $totalDeduction)) - $salaryAmount, 2)
                            ]) }}
                    </p>
                </div>
            </div>
        @endif
        @if($payslip->net_salary - ($totalAllowance - $totalDeduction) < $salaryAmount)
            <div class="custom-row basic-salary-info border-bottom mt-3">
                <div class="column">
                    <p class="mb-1">{{ __t('unpaid_leave_deduction') }}</p>
                </div>
                <div class="column text-right">
                    <p class="mb-1 currency-symbol">
                        {{ trans('default.like_'.$settings->currency_position, [
                            'symbol' => $settings->currency_symbol,
                             'amount' => round($salaryAmount - ($payslip->net_salary - ($totalAllowance - $totalDeduction)), 2)
                            ]) }}
                    </p>
                </div>
            </div>
        @endif
        <div class="custom-row basic-salary-info border-bottom mt-3">
            <div class="column">
                <p class="mb-1">{{ __t('total_earning') }}
                    <small>({{__t('based_on') .' '. __t($payslip->consider_type)}}{{
                            ($payslip->net_salary - ($totalAllowance - $totalDeduction)) >= $salaryAmount
                            ? ', '.($payslip->consider_overtime ? __t('included_overtime') : __t('excluded_overtime'))
                            : ''}})
                    </small>
                </p>
            </div>
            <div class="column text-right">
                <p class="mb-1 currency-symbol">
                    {{ trans('default.like_'.$settings->currency_position, [
                        'symbol' => $settings->currency_symbol,
                         'amount' => round($payslip->net_salary - ($totalAllowance - $totalDeduction), 2)
                        ]) }}
                </p>
            </div>
        </div>
    @endif

    @if(count($beneficiaries) != 0)
        <div class="custom-row benificiary-info border-bottom mt-5 mb-2">
            <div class="column">
                <p class="mb-1">{{ __t('beneficiary') }}</p>
            </div>
        </div>
        <div class="custom-row salary-details-info">
            <div class="column earnings-info pr-3">
                <table>
                    <thead>
                    <tr>
                        <td>
                            <span class="w-100 d-inline-block border-bottom pb-1">{{ __t('allowances') }}</span>
                        </td>
                        <td class="text-right">
                            <span class="w-100 d-inline-block border-bottom pb-1 mt-10 text-white"
                                  style="margin-left: -2px;"> - </span>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($beneficiaries as $beneficiary)
                        @if($beneficiary->beneficiary->type == 'allowance')
                            @if($beneficiary->is_percentage == 1)
                                <tr>
                                    <td class="small">{{ $beneficiary->beneficiary->name }} ({{ $beneficiary->amount }}
                                        %)
                                    </td>
                                    <td class="small text-right currency-symbol">
                                        {{ trans('default.like_'.$settings->currency_position, [
                                            'symbol' => $settings->currency_symbol,
                                            'amount' => round(($salaryAmount/100) * $beneficiary->amount)
                                                ]) }}
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td class="small">{{ $beneficiary->beneficiary->name }}</td>
                                    <td class="small text-right currency-symbol">
                                        {{ trans('default.like_'.$settings->currency_position, [
                                           'symbol' => $settings->currency_symbol,
                                           'amount' =>  $beneficiary->amount
                                               ]) }}
                                    </td>
                                </tr>
                            @endif
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="column deductions-info pl-3">
                <table>
                    <thead>
                    <tr>
                        <td>
                            <span class="w-100 d-inline-block border-bottom pb-1">{{ __t('deductions') }}</span>
                        </td>
                        <td class="text-right">
                            <span class="w-100 d-inline-block border-bottom pb-1 text-white" style="margin-left: -2px;">-</span>
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($beneficiaries as $beneficiary)
                        @if($beneficiary->beneficiary->type == 'deduction')
                            @if($beneficiary->is_percentage == 1)
                                <tr>
                                    <td class="small">{{ $beneficiary->beneficiary->name }} ({{ $beneficiary->amount }}
                                        %)
                                    </td>
                                    <td class="small text-right currency-symbol">
                                        {{ trans('default.like_'.$settings->currency_position, [
                                          'symbol' => $settings->currency_symbol,
                                          'amount' =>  round(($salaryAmount/100) * $beneficiary->amount, 2)
                                              ]) }}
                                    </td>
                                </tr>
                            @else
                                @php
                                    settings()
                                @endphp
                                <tr>
                                    <td class="small">{{ $beneficiary->beneficiary->name }}</td>
                                    <td class="small text-right currency-symbol">
                                        {{ trans('default.like_'.$settings->currency_position, [
                                          'symbol' => $settings->currency_symbol,
                                          'amount' =>  $beneficiary->amount
                                              ]) }}
                                    </td>
                                </tr>
                            @endif
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="custom-row earning-deduction-info mb-3">
            <div class="column pr-3">
                <table>
                    <tbody>
                    <tr>
                        <td class="pt-2">{{ __t('total_allowance') }}</td>
                        <td class="text-right pt-2 currency-symbol">
                            {{ trans('default.like_'.$settings->currency_position, [
                                'symbol' => $settings->currency_symbol,
                                'amount' =>  round($totalAllowance, 2)
                                    ]) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="column pl-3">
                <table>
                    <tbody>
                    <tr>
                        <td class="pt-2">{{ __t('total_deduction') }}</td>
                        <td class="text-right pt-2 currency-symbol">
                            {{ trans('default.like_'.$settings->currency_position, [
                                'symbol' => $settings->currency_symbol,
                                'amount' =>  round($totalDeduction, 2)
                                    ]) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="custom-row total-benificiary-info border-bottom">
            <div class="column">
                <p class="mb-1">{{ __t('beneficiary_amount') }}</p>
            </div>
            <div class="column text-right">
                <p class="mb-1 currency-symbol">
                    {{ trans('default.like_'.$settings->currency_position, [
                            'symbol' => $settings->currency_symbol,
                            'amount' =>  round($totalAllowance - $totalDeduction, 2)
                                ]) }}
                </p>
            </div>
        </div>
    @endif

    <div class="custom-row net-salary-info border-bottom mb-5 mt-5">
        <div class="column">
            <p class="mb-1">{{ __t('net_payable_salary') }}</p>
        </div>
        <div class="column text-right">
            <p class="mb-1 currency-symbol">
                {{ trans('default.like_'.$settings->currency_position, [
                        'symbol' => $settings->currency_symbol,
                        'amount' =>  round($payslip->net_salary, 2)
                            ]) }}
            </p>
        </div>
    </div>

    @if(property_exists($payslip_settings, 'note') && $payslip_settings->note)
        <hr class="mb-0">
        <small class="font-italic"><b>Note: </b>{{ $payslip_settings->note }}</small>
    @endif
</div>

</body>
</html>