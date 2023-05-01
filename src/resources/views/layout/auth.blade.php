@extends('common.master')

@section('master')
    <div class="row">

        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-8">
            @php
                $banner = settings('tenant_banner', 'app_banner');
                $banner = $banner ? asset($banner) : asset('images/default-banner.png');
            @endphp
            <div class="back-image"
                 style="background-image: url({{ $banner }})">
            </div>
        </div>

        <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-4 pl-md-0">
            @yield('contents')
        </div>

    </div>
@endsection

