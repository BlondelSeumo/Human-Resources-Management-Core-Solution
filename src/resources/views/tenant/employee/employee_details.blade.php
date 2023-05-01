@extends('layout.tenant')

@section('title', __t('employee_details'))

@section('contents')
    <app-employee-details
            :employee-id="{{$employee_id}}"
            :manager-dept="{{ json_encode($manager_dept) }}"
    ></app-employee-details>
@endsection