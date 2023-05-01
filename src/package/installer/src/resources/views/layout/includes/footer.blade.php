<footer>
    <script>
        window.settings = {!! isset($settings) ? json_encode($settings): '{}' !!}

        window.localStorage.setItem('app-language', '{!! app()->getLocale() ?? "en" !!}');

        window.localStorage.setItem('app-languages',
            JSON.stringify(
                {!! json_encode(include resource_path().DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.(app()->getLocale() ?? 'en').DIRECTORY_SEPARATOR.'default.php') !!}
            )
        );

        window.appLanguage = window.localStorage.getItem('app-language');
        window.localStorage.setItem('base_url', '{{request()->root()}}');

        @if(env('APP_INSTALLED') && auth()->user())
        window.localStorage.setItem('permissions', JSON.stringify(
                {!! json_encode(array_merge(
                    resolve(\App\Repositories\Core\Auth\UserRepository::class)->getPermissionsForFrontEnd(),
                    [
                        'is_app_admin' => auth()->user()->isAppAdmin()
                    ])) !!}

        ))
        @endif

    </script>
    <script src="{{asset('js/core.js')}}"></script>
    <script src="{{asset('install/assets/js/installer.js')}}"></script>
   {{-- @stack('before-scripts')
    {!! script(mix('js/manifest.js')) !!}
    {!! script(mix('js/vendor.js')) !!}
    {!! script(mix('js/core.js')) !!}
    {!! script('vendor/summernote/summernote-bs4.js') !!}

    @stack('after-scripts')--}}
</footer>
