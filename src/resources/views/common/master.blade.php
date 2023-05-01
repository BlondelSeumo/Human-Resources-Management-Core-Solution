<!doctype html>
<html lang="<?php  app()->getLocale(); ?>">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    {{--<meta name="csrf-token" content="{{ csrf_token() }}">--}}
    <link rel="shortcut icon" href="{{ url(settings('tenant_icon', 'app_icon')) }}" />
    <link rel="apple-touch-icon" href="{{ url(settings('tenant_icon', 'app_icon')) }}" />
    <link rel="apple-touch-icon-precomposed" href="{{ url(settings('tenant_icon', 'app_icon')) }}" />

    <title>@yield('title') - {{ settings('tenant_name', 'app_name') }}</title>
    @stack('before-styles')
    {{ style(mix('css/core.css')) }}
    {{ style(mix('css/fontawesome.css')) }}
    {{ style(mix('css/dropzone.css')) }}
    {{ style('vendor/summernote/summernote-bs4.css') }}
    @stack('after-styles')
</head>
<body>
<div id="app" class="@yield('class')">
    @yield('master')
</div>
@guest()
    <script>
        window.localStorage.removeItem('permissions');
    </script>
@endguest

@auth()
    <script>
        window.localStorage.setItem('permissions', JSON.stringify(
                {!! json_encode(array_merge(
                        resolve(\App\Repositories\Core\Auth\UserRepository::class)->getPermissionsForFrontEnd(),
                        [
                            'is_app_admin' => auth()->user()->isAppAdmin(),
                            //'is_brand_admin' => auth()->user()->isBrandAdmin(optional(brand())->id)
                        ]
                    )
                )
                !!}
        ))

        window.onload = function () {
            window.scroll({
                top: 0,
                left: 0,
                behavior: 'smooth'
            })
        }
    </script>
@endauth

<script>
    window.localStorage.setItem('app-language', '{!! app()->getLocale() ?? "en"; !!}');
    window.localStorage.setItem('app-languages',
        JSON.stringify(
                {!! json_encode(include resource_path().DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.(session()->get('locale') ?: settings('language') ?: "en").DIRECTORY_SEPARATOR.'default.php') !!}
            )
        );
        window.localStorage.setItem('base_url', '{!! request()->root() !!}');
    window.appLanguage = window.localStorage.getItem('app-language');
</script>
@stack('before-scripts')
{!! script(mix('js/manifest.js')) !!}
{!! script(mix('js/vendor.js')) !!}
{!! script(mix('js/core.js')) !!}
{!! script('vendor/summernote/summernote-bs4.js') !!}
@stack('after-scripts')
</body>
</html>

