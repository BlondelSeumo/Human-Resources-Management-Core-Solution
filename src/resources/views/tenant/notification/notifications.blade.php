@extends('layout.tenant')

@section('title', __t('notifications'))

@section('contents')
    <app-notifications :unread="{{ $unread ? 1 : 0}}"></app-notifications>
@endsection
