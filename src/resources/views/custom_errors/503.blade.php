@extends('custom_errors.master')

@section('title', '503 - Service unavailable')

@php

@endphp

@section('error-content')

    <app-error error-type="503"
               error-title="Service unavailable"
               error-description="{{ isset($message) ? $message : 'Lorem Ipsum is simply dummy text of the printing and typesetting industry' }}"
               url="{{ url()->previous() ?? '/' }}"
    >
    </app-error>
@endsection
