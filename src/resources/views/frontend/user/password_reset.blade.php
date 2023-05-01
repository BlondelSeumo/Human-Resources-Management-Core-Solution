@extends('layout.auth')

@section('title', trans('default.reset_password'))

@section('contents')
    @php
        ['logo' => $logo] = \App\Http\Composer\Helper\LogoIcon::new(true)->logoIcon();
    @endphp
    <app-password-reset logo-url="{{ $logo }}"
                        app-name="{{ settings('tenant_name', 'app_name') }}"></app-password-reset>
@endsection
