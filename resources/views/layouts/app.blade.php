<!DOCTYPE html>
<html @yield('amp', '') lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('metaTitle', config('app.name'))</title>

    <meta name="description" content="@yield('metaDescription')"/>
@yield('metaHeader')
<!-- Styles -->
    @section('stylesheets')
        <link href="{{ mix('css/lib.css') }}" rel="stylesheet">
        <link href="{{ mix('css/main.css') }}" rel="stylesheet">
    @show
    @yield('header')
</head>
<body class="@yield('bodyClass')">
<div id="page-wrapper">
    @include('partials/header')
    <section id="content" class="gray-area">
        @yield('topcontainer')

        <div class="container">

            @include('partials/alert-box')

            @yield('content')

        </div>

        @yield('belowcontainer')

    </section>
</div>

@include('partials/footer')
<!-- Scripts -->
<script>
    window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};

</script>
<!-- Scripts -->
<script src="{{ mix('js/main.js') }}"></script>

@yield('beforebody')

@yield('endbody')
@if (env('APP_ENV') == 'production')
    <script>
        (function(i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function() {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-106322751-1', 'auto');
        ga('send', 'pageview');

    </script>
@endif
<!-- Load Facebook SDK for JavaScript -->
{{--<div id="fb-root"></div>--}}
{{--<script>(function(d, s, id) {--}}
{{--        var js, fjs = d.getElementsByTagName(s)[0];--}}
{{--        if (d.getElementById(id)) return;--}}
{{--        js = d.createElement(s); js.id = id;--}}
{{--        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js#xfbml=1&version=v2.12';--}}
{{--        fjs.parentNode.insertBefore(js, fjs);--}}
{{--    }(document, 'script', 'facebook-jssdk'));</script>--}}

{{--<!-- Your customer chat code -->--}}
{{--<div class="fb-customerchat"--}}
{{--     attribution=setup_tool--}}
{{--     page_id="120233208692338"></div>--}}
</body>
</html>
