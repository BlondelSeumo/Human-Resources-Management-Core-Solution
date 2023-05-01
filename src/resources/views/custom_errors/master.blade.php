<!doctype html>
<html lang="<?php  app()->getLocale(); ?>">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>@yield('title')</title>
    @stack('before-styles')
    {{ style(mix('css/dropzone.css')) }}
    {{ style(mix('css/core.css')) }}
    {{ style(mix('css/fontawesome.css')) }}
    @stack('after-styles')

    <style>
        html, body {
            height: 100%;
        }
    </style>
</head>
<body>

<div id="app" class="h-100">
    @yield('error-content')
</div>

<script>
    window.localStorage.setItem('app-languages',
        JSON.stringify(
            {!! json_encode(include resource_path().DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.(app()->getLocale() ?? 'en').DIRECTORY_SEPARATOR.'default.php') !!}
        )
    );
</script>
@stack('before-scripts')
{!! script(mix('js/manifest.js')) !!}
{!! script(mix('js/vendor.js')) !!}
{!! script(mix('js/core.js')) !!}
@stack('after-scripts')
</body>
</html>
