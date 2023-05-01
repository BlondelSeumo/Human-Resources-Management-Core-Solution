@extends('custom_errors.master')

@section('title', '403 - Forbidden')

@php

@endphp

@section('error-content')

    <app-error error-type="403"
               error-title="Forbidden"
               error-description="{{ isset($message) ? $message : 'Lorem Ipsum is simply dummy text of the printing and typesetting industry' }}"
               url="{{ url()->previous() ?? '/' }}"
    >
    </app-error>
@endsection
