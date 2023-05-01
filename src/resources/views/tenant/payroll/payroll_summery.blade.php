@extends('layout.tenant')

@section('title', __t('payroll_summery'))

@section('contents')
    <app-payroll-summery
            first-user="{{ json_encode($user) }}"
    ></app-payroll-summery>
@endsection

