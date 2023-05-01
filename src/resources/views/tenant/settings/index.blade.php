@extends('layout.tenant')

@section('title', __t('app_settings'))

@push('before-styles')
    {{ style('vendor/summernote/summernote-lite.css') }}
@endpush

@section('contents')
    <app-tenant-settings-layout></app-tenant-settings-layout>
@endsection
