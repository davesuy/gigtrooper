<!DOCTYPE html>
<html ⚡ lang="{{ config('app.locale') }}">
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
    @include('partials/header-amp')

    <section id="content" class="gray-area">
        <div class="container">

            @include('partials/alert-box')

            @yield('content')

        </div>

        @yield('belowcontainer')

    </section>
</div>

@include('partials/footer-amp')

@yield('beforebody')

@yield('endbody')
@if (env('APP_ENV') == 'production')
    <amp-analytics type="googleanalytics">
        <script type="application/json">
  {
    "vars": {
      "account": "UA-106322751-1"
    },
    "triggers": {
      "trackPageview": {
        "on": "visible",
        "request": "pageview"
      },
      "trackEvent": {
        "selector": "#event-test",
        "on": "click",
        "request": "event",
        "vars": {
          "eventCategory": "ui-components",
          "eventAction": "click"
        }
      }
    }
  }
  </script>
</amp-analytics>
@endif
<amp-sidebar layout="nodisplay" side="right" id="mobile-menu-01" class="mobile-menu">
    <div role="button" aria-label="close sidebar" on="tap:mobile-menu-01.toggle" tabindex="0" class="close-sidebar">✕</div
    <ul class="sidebar menu">
        <li>
            <a href="/how-it-works">HOW IT WORKS</a>
        </li>
        <li class="menu-item-has-children">
            <a href="/search/members/all/all">SEARCH</a>
        </li>
        <li>
            <a href="/blog">BLOG</a>
        </li>
        <li>
            <a href="/contact-us">CONTACT US</a>
        </li>
        @if (Auth::guest())
            <li>
                <a href="/login">LOGIN</a>
            </li>
            <li>
                <a href="/register">JOIN</a>
            </li>
        @else
            <li>
                <a href="/account/profile"><i class="soap-icon-user circle"></i> Profile</a>
            </li>
        @endif
    </ul>
</amp-sidebar>
</body>
</html>
