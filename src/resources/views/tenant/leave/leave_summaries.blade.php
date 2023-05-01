@extends('layout.tenant')

@section('title', __t('summery'))

@section('contents')
    <app-leave-summary
            first-user="{{ json_encode($user) }}"
            :manager-dept="{{ json_encode($manager_dept) }}"
            leave-id="{{ request()->query('leave_id') }}"
    ></app-leave-summary>
@endsection
