@extends('installer::installer')

@section('contents')
    <app-pusher-setup-wizard
            app-name="{{ config('app.name') }}"
            installer-config="{{ json_encode(config('installer')) }}"
    ></app-pusher-setup-wizard>
@endsection
