@extends('layout.tenant')

@section('title', __t('payslip'))

@section('contents')
    <app-payslip payrun="{{ request()->get('payrun') }}"></app-payslip>
@endsection

