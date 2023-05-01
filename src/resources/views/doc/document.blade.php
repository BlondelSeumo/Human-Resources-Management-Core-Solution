<!doctype html>
<html lang="<?php  app()->getLocale(); ?>">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
        <title>Welcome to Gain Core Apps</title>
        @stack('before-styles')
        {{ style(mix('css/dropzone.css')) }}
        {{ style(mix('css/core.css')) }}
        {{ style(mix('css/fontawesome.css')) }}
        @stack('after-styles')
    </head>
    <body>
        <div id="app">
            <div class="container-scroller">
                <nav class="sidebar sidebar-offcanvas" id="sidebar">
                    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                        <a class="navbar-brand brand-logo">
                            <img src="{{ asset('images/core.png') }}" alt="logo"/>
                        </a>
                    </div>
                    <ul class="nav nav-scrolling">
                        @foreach ($components as $component)
                            @if(basename($component) !='.' && basename($component) !='..')
                                @if(!in_array($component,$not_completed_components))
                                    <li class="{{ ($component_name ==$component) ? "nav-item active" : "nav-item" }}">
                                        <a class="nav-link"
                                           href="{{url('')}}/doc/core/components/{{basename($component)}}">
                                            <span class="icon-wrapper rounded">
                                                <app-icon name="file-plus" class="menu-icon"/>
                                            </span>
                                            <span class="menu-title" style="white-space: initial;">{{$component}}</span>
                                        </a>
                                    </li>
                                @else
                                    <li class="{{ ($component_name ==$component) ? "nav-item active" : "nav-item" }}">
                                        <a class="nav-link"
                                           href="{{url('')}}/doc/core/components/{{basename($component)}}">
                                            <span class="icon-wrapper">
                                                <app-icon name="file-minus" class="menu-icon"/>
                                            </span>
                                            <span class="menu-title" style="white-space: initial;">{{$component}}</span>
                                        </a>
                                    </li>
                                @endif
                            @endif
                        @endforeach
                        <li class="nav-item text-light bg-dark py-3">
                            Total components: <code class="ml-2">{{ count($components)}}</code><br>
                            Readme not done: <code class="ml-2">{{ count($not_completed_components)}}</code>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid page-body-wrapper pt-0">
                    <div class="main-panel">
                        <div class="container p-3">{!! $content !!}</div>
                    </div>
                </div>
            </div>
        </div>

        @stack('before-scripts')
        {!! script(mix('js/manifest.js')) !!}
        {!! script(mix('js/vendor.js')) !!}
        {!! script(mix('js/core.js')) !!}
        @stack('after-scripts')
    </body>
</html>
