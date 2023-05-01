@extends('layout.tenant')

@section('title', __t('calendar'))

@section('contents')
    <app-leave-calendar
            :manager-dept="{{ json_encode($manager_dept) }}"
    ></app-leave-calendar>
@endsection
