@extends('custom_errors.master')

@section('title', '400 - Bad request')

@php

@endphp

@section('error-content')

    <app-error error-type="400"
               error-title="Bad request"
               error-description="{{ isset($message) ? $message : 'Lorem Ipsum is simply dummy text of the printing and typesetting industry' }}"
               url="{{ url()->previous() ?? '/' }}"
    >
    </app-error>
@endsection
