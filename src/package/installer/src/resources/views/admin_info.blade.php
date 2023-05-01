@extends('installer::installer')

@section('contents')
    <app-admin-wizard
            app-name="{{ config('app.name') }}"
            installer-config="{{ json_encode(config('installer')) }}"
    ></app-admin-wizard>

@endsection