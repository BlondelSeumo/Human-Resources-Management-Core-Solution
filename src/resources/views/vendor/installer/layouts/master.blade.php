@inject('app', 'App\Http\Controllers\API\SettingController')
 <?php
 $publicPath = $app->getAppPublicPath();
 ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@if (trim($__env->yieldContent('template_title')))@yield('template_title') | @endif {{ trans('installer_messages.install_title') }}</title>
    <link rel="icon" type="image/png" href="{{ asset($publicPath.'/installer/img/favicon/favicon-16x16.png') }}" sizes="16x16"/>
    <link rel="icon" type="image/png" href="{{ asset($publicPath.'/installer/img/favicon/favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ asset($publicPath.'/installer/img/favicon/favicon-96x96.png') }}" sizes="96x96"/>
    <link href="{{ asset($publicPath.'/installer/css/style.css') }}" rel="stylesheet"/>
    <style>
        .buttonHolder{
            text-align: center;
            margin:0 auto;
        }
        #btn_s{
            background-color: #4a97fd;
            border-radius: 2px;
            padding: 10px 20px !important;
            color: #fff;
            box-shadow: 0 1px 1.5px rgba(0, 0, 0, 0.12), 0 1px 1px rgba(0, 0, 0, 0.24);
            text-decoration: none;
            outline: none;
            border: none;
            transition: box-shadow .2s ease, background-color .2s ease;
            cursor: pointer;
            font-size: 1.4rem !important;
            font-weight: 500;
        }
    </style>
    @yield('style')
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!}
    </script>
</head>
<body>
<div class="master">
    <div class="box">
        <div class="header">
            <h1 class="header__title">@yield('title')</h1>
        </div>
        <ul class="step">
            <li class="step__divider"></li>
            <li class="step__item {{ isActive('LaravelInstaller::final') }}">
                <i class="step__icon fa fa-server" aria-hidden="true"></i>
            </li>

            {{--<li class="step__divider"></li>
            <li class="step__item {{ isActive('LaravelInstaller::InstallNow')}}">
                @if(Request::is('install/install-now') || /*Request::is('install/environment/wizard') || */ Request::is('install/environment/classic') )
                    <a href="{{ route('LaravelInstaller::InstallNow') }}">
                        <i class="step__icon fa fa-globe" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-globe" aria-hidden="true"></i>
                @endif
            </li>--}}

            <li class="step__divider"></li>
            <li class="step__item {{ isActive('LaravelInstaller::environmentWizard')}}">
                @if(Request::is('/install/environment/wizard') )
                    <a href="{{ route('LaravelInstaller::environmentWizard') }}">
                        <i class="step__icon fa fa-cog" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-cog" aria-hidden="true"></i>
                @endif
            </li>

            <li class="step__divider"></li>
            <li class="step__item {{ isActive('LaravelInstaller::user') }}">
                @if(Request::is('install/user') )
                    <a href="{{ route('LaravelInstaller::user') }}">
                        <i class="step__icon fa fa-user" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-user" aria-hidden="true"></i>
                @endif
            </li>

            <li class="step__divider"></li>
            <li class="step__item {{ isActive('LaravelInstaller::purchase') }} ">
                @if(Request::is('install/purchase') )
                    <a href="{{ route('LaravelInstaller::purchase') }}">
                        <i class="step__icon fa fa-shopping-cart" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-shopping-cart" aria-hidden="true"></i>
                @endif
            </li>

            <li class="step__divider"></li>
            <li class="step__item {{ isActive('LaravelInstaller::permissions') }}">
                @if(Request::is('install/permissions') )
                    <a href="{{ route('LaravelInstaller::permissions') }}">
                        <i class="step__icon fa fa-file" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-file" aria-hidden="true"></i>
                @endif
            </li>
            {{--<li class="step__divider"></li>
            <li class="step__item {{ isActive('LaravelInstaller::requirements') }}">
                @if(Request::is('install') || Request::is('install/requirements') )
                    <a href="{{ route('LaravelInstaller::requirements') }}">
                        <i class="step__icon fa fa-list" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-list" aria-hidden="true"></i>
                @endif
            </li>--}}
            <li class="step__divider"></li>
            <li class="step__item {{ isActive('LaravelInstaller::welcome') }}">
                @if(Request::is('install') || Request::is('install/requirements'))
                    <a href="{{ route('LaravelInstaller::welcome') }}">
                        <i class="step__icon fa fa-home" aria-hidden="true"></i>
                    </a>
                @else
                    <i class="step__icon fa fa-home" aria-hidden="true"></i>
                @endif
            </li>
            <li class="step__divider"></li>
        </ul>
        <div class="main">

            @if (session('error'))
                <p class="alert alert-danger text-center">
                    <strong>
                            {{ session('error') }}
                    </strong>
                </p>
            @endif

            @yield('container')
        </div>
    </div>
</div>
@yield('scripts')
<script type="text/javascript">

</script>
</body>
</html>
