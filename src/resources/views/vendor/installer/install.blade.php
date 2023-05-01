@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.install_now') }}
@endsection

@section('title')
    {{ trans('installer_messages.install_now') }}
@endsection

@section('container')
    <p class="text-center welcome-message">
        {{ trans('installer_messages.ready_for_install') }}
    </p>
    <p class="text-center">
        <a href="{{ route('LaravelInstaller::database') }}" class="button">
            <i class="fa fa-check fa-fw" aria-hidden="true"></i>
            {!! trans('installer_messages.install_now') !!}
            <i class="fa fa-angle-double-right fa-fw" aria-hidden="true"></i>
        </a>
    </p>
@endsection
