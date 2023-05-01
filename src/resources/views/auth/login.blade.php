@extends('layout.auth')

@section('title', trans('default.login'))

@section('contents')
    @php
        ['logo' => $logo] = \App\Http\Composer\Helper\LogoIcon::new(true)->logoIcon();
    @endphp
   <app-auth-login
            logo-url="{{ $logo }}"
            demo="{{ count($demo) ? json_encode($demo) : ''}}"
            app-name="{{ settings('tenant_name', 'app_name') }}"
            previous-page="{{ $previous_page ?? '/' }}"
    ></app-auth-login>

@endsection

