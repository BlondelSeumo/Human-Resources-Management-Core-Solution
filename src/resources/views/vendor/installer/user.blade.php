@extends('vendor.installer.layouts.master')
@section('template_title')
    Step 4 | Admin Login Info
@endsection

@section('title')
    <i class="fa fa-user fa-fw" aria-hidden="true"></i>
    Admin Login Info
@endsection

@section('container')
    <div class="tabs tabs-full">

        <form method="post" action="{{ route('LaravelInstaller::userSaveWizard') }}" class="">
            <div class="" id="">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group {{ $errors->has('first_name') ? ' has-error ' : '' }}">
                    <label for="first_name">
                        First Name
                    </label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" placeholder="Type your first name" />
                    @if ($errors->has('first_name'))
                        <span class="error-block">
                            {{ $errors->first('first_name') }}
                        </span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('last_name') ? ' has-error ' : '' }}">
                    <label for="last_name">
                        Last Name
                    </label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" placeholder="Type your last name" />
                    @if ($errors->has('first_name'))
                        <span class="error-block">
                            {{ $errors->first('last_name') }}
                        </span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('email') ? ' has-error ' : '' }}">
                    <label for="email">
                        Login Email
                    </label>
                    <input type="text" name="email" id="email" value="{{ old('email') }}" placeholder="Type login email" />
                    @if ($errors->has('email'))
                        <span class="error-block">
                            {{ $errors->first('email') }}
                        </span>
                    @endif
                </div>
                <div class="form-group {{ $errors->has('password') ? ' has-error ' : '' }}">
                    <label for="password">
                        Login Password
                    </label>
                    <input type="password" name="password" id="password" value="{{ old('password') }}" placeholder="Type login password" />
                    @if ($errors->has('password'))
                        <span class="error-block">
                            {{ $errors->first('password') }}
                        </span>
                    @endif
                </div>

                <div class="buttons">
                    <button type="submit" class="button">
                        Configure Database
                        <i class="fa fa-angle-right fa-fw" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </form>

    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

    </script>
@endsection
