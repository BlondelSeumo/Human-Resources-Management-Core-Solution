@extends('installer::installer')

@section('contents')
    <app-database-wizard
            app-name="{{ config('app.name') }}"
            installer-config="{{ json_encode(config('installer')) }}"
    ></app-database-wizard>
@endsection