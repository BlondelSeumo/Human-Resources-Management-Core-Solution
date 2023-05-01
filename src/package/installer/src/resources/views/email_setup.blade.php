@extends('installer::installer')

@section('contents')
    <app-email-setup-wizard
            app-name="{{ config('app.name') }}"
            installer-config="{{ json_encode(config('installer')) }}"
    ></app-email-setup-wizard>
@endsection
