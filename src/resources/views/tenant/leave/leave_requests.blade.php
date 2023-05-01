@extends('layout.tenant')

@section('title', __t('leave_request'))

@section('contents')
    <app-leave-requests
            leave-id="{{ request()->query('leave_id') }}"
            :manager-dept="{{ json_encode($manager_dept) }}"
    ></app-leave-requests>
@endsection
