@extends('layout.tenant')

@section('title', __t('summery'))

@section('contents')
    <app-attendance-summery
            first-user="{{ json_encode($user) }}"
            details-id="{{ request()->query('details_id') }}"
            attendance-id="{{ request()->query('id') }}"
    ></app-attendance-summery>
@endsection
