@extends('layout.tenant')

@section('title', __t('attendance_request'))

@section('contents')
    <app-attendance-request
            details-id="{{ request()->query('details_id') }}"
            attendance-id="{{ request()->query('id') }}">
    </app-attendance-request>
@endsection
