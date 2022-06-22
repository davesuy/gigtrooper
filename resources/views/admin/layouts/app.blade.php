<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/admin-main.css') }}" rel="stylesheet">
    <link href="{{ mix('css/lib.css') }}" rel="stylesheet">
    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    @yield('header')
</head>
<body>
<div id="page-wrapper">
    @include('partials/header')
    <section id="content" class="gray-area">
        <div class="container">
            @if (!Auth::guest())
                <div class="pull-right">
                    <a class="button btn-medium" href="{{ url('/logout') }}"
                       onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                </div>
                <div class="clearfix"></div>
            @endif
            <div id="main">
                @include('partials/alert-box')
                <div class="tab-container arrow-left dashboard">
                    <ul class="tabs">
                        <li {!! TemplateHelper::setActive('profile') !!}>
                            <a href="/account/profile" aria-expanded="false">
                                <i class="soap-icon-user circle"></i> Profile</a>
                        </li>
                        <li {!! TemplateHelper::setActive('messages') !!}>
                            <a href="/account/messages" aria-expanded="false">
                                <i class="soap-icon-message circle"></i> Messages</a>
                        </li>
                        @if (TemplateHelper::isUserRole('administrator')
                        || TemplateHelper::isUserRole('superAdmin'))
                        <li {!! TemplateHelper::setActive('quotes') !!}>
                            <a href="/{{ config('app.cp') }}/quotes" aria-expanded="false">
                                <i class="soap-icon-message circle"></i> Quotes</a>
                        </li>
                        @endif
                        @if (TemplateHelper::isUserRole('administrator')
                        || TemplateHelper::isUserRole('superAdmin'))
                            <li {!! TemplateHelper::setActive('dashboard') !!}>
                                <a href="/{{ config('app.cp') }}/dashboard" aria-expanded="false">
                                    <i class="soap-icon-anchor circle"></i> Dashboard</a></li>
                            <li {!! TemplateHelper::setActive('users') !!}>
                                <a href="/{{ config('app.cp') }}/users" aria-expanded="false">
                                    <i class="soap-icon-conference circle"></i> Users</a></li>
                            <li {!! TemplateHelper::setActive('member-categories') !!}>
                                <a style="line-height: 1.5"
                                   href="/{{ config('app.cp') }}/member-categories">
                                    <i class="soap-icon-tree circle"></i> Member Categories</a></li>
                            <li {!! TemplateHelper::setActive('pages') !!}><a
                                        href="/{{ config('app.cp') }}/pages"
                                        aria-expanded="true"><i
                                            class="soap-icon-businessbag circle"></i> Pages</a></li>
                            <li {!! TemplateHelper::setActive('countries') !!}><a href="/{{ config('app.cp')
                            }}/countries"><i class="soap-icon-tree circle"></i> Countries</a></li>
                        @endif
                        @if (TemplateHelper::isUserRole('administrator')
                        || TemplateHelper::isUserRole('blogger')
                        || TemplateHelper::isUserRole('superAdmin')
                        )
                            <li {!! TemplateHelper::setActive('blog') !!}><a
                                        href="/{{ config('app.cp') }}/blog"><i
                                            class="soap-icon-wishlist circle"></i> Blog</a></li>
                            <li {!! TemplateHelper::setActive('categories') !!}><a href="/{{ config('app.cp')
                            }}/categories"><i class="soap-icon-tree circle"></i> Categories</a></li>
                        @endif
                    </ul>
                    <div class="tab-content">
                        @yield('content')
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

@include('partials/footer')
<!-- Scripts -->
<script>
    var CKEDITOR_BASEPATH = '/ckeditor/';
</script>
<!-- Scripts -->
<script src="{{ mix('js/admin-main.js') }}"></script>

<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script> -->

@include('partials/jscripts')

@yield('beforebody')

@yield('endbody')

<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js#xfbml=1&version=v2.12';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

<!-- Your customer chat code -->
<div class="fb-customerchat"
     attribution=setup_tool
     page_id="120233208692338"></div>
</body>
</html>

</body>
</html>
