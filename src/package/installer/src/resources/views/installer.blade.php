<html lang="
<?php
app()->getLocale();

//Let the resource to be loaded
sleep(1);
?>">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>

    <link rel="shortcut icon" href=" {{ request()->root().'/images/icon.png' }}"/>
    <link rel="apple-touch-icon" href=" {{ request()->root().'/images/icon.png' }}"/>
    <link rel="apple-touch-icon-precomposed" href=" {{ request()->root().'/images/icon.png' }}"/>

    <title>{{ trans('default.install') }} - {{ config('app.name') }}</title>

    @include('installer::layout.includes.header')
</head>
<body>
<div id="app">
    <div class="container">
        @yield('contents')
    </div>
</div>

@include('installer::layout.includes.footer')
</body>
</html>
