@extends('installer::installer')

@section('contents')
    <app-purchase-code-wizard
            app-name="{{ config('app.name') }}"
            installer-config="{{ json_encode(config('installer')) }}"
    ></app-purchase-code-wizard>
@endsection