@extends('installer::installer')

@section('contents')
<app-additional-requirement-wizard
    app-name="{{ config('app.name') }}"
    symlink-documentation="{{ config('installer.link.symlink_requirement') }}"
></app-additional-requirement-wizard>

@endsection