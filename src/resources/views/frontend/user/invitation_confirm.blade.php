@extends('layout.auth')

@section('title', trans('default.confirm'))

@section('contents')
    @php
        ['logo' => $logo] = \App\Http\Composer\Helper\LogoIcon::new(true)->logoIcon();
    @endphp
    <app-user-invite-confirm :user="{{ json_encode($user)  }}"
                             logo-url="{{ $logo }}"
                             app-name="{{ settings('tenant_name', 'app_name') }}"
    ></app-user-invite-confirm>
@endsection
