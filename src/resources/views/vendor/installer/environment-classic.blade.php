@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.environment.classic.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-code fa-fw" aria-hidden="true"></i> {{ trans('installer_messages.environment.classic.title') }}
@endsection

@section('container')

    <form method="post" action="{{ route('LaravelInstaller::environmentSaveClassic') }}">
        {!! csrf_field() !!}
        <label>Please set the configuration. Don't change the APP_ENV</label>
        <textarea class="textarea" name="envConfig">{{ $envConfig }}</textarea>
        <div class="buttons">
            <button type="submit" class="button">
                <i class="fa fa-floppy-o fa-fw" aria-hidden="true"></i>
                {!! trans('installer_messages.environment.classic.save') !!}
                <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
            </button>
        </div>
    </form>



@endsection