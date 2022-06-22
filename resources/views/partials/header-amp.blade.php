<header id="header" class="navbar-static-top">
    <div class="topnav hidden-xs">
        <div class="container">
            <ul class="quick-menu pull-left">
                <li>&nbsp;</li>
            </ul>
            <ul class="quick-menu pull-right">
                <!-- Authentication Links -->
                @if (!Auth::guest())
                    <li class="ribbon">
                    <a href="#">{{ Auth::user()->name }}</a>
                        <ul class="menu mini uppercase">
                            <li><a href="/account/profile"
                                        class="location-reload">Profile</a></li>
                            <li>
                                <a href="{{ url('/logout') }}">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif

            </ul>
        </div>
    </div>

    <div class="main-header">

        <a href="#mobile-menu-01" on="tap:mobile-menu-01.toggle"
           tabindex="0"
           class="mobile-menu-toggle">
            Mobile Menu Toggle
        </a>

        <div class="container">
            <h1 class="logo navbar-brand">
                <a href="/" title="Gigtrooper - home">
                    <amp-img alt="Gigtrooper logo"
                               width="160"
                               height="37"
                               layout="responsive"
                               src="/images/logo.png"></amp-img>
                </a>
            </h1>

            <nav id="main-menu" role="navigation">
                <ul class="menu">
                    <li class="menu-item-has-children">
                        <a href="/how-it-works">HOW IT WORKS</a>
                    </li>
                    <li class="menu-item-has-children">
                        <a href="/search/members/{{ \App::make('countryService')->getSessionCountry() }}/all">SEARCH</a>
                        @php
                            $getMemberCategoryMenu = \TemplateHelper::getMemberCategoryMenu();
                        @endphp
                        {!! $getMemberCategoryMenu !!}
                    </li>
                    <li class="menu-item-has-children">
                        <a href="/blog">BLOG</a>
                        @php
                            // $getCategoryMenu = \TemplateHelper::getCategoryMenu();
                        @endphp
                        {{--{!! $getCategoryMenu !!}--}}
                    </li>
                    <li class="menu-item-has-children">
                        <a href="/contact-us">CONTACT US</a>
                    </li>

                    @if (Auth::guest())
                        <li class="menu-item-has-children">
                            <a href="/login">LOGIN</a>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="/register">JOIN</a>
                        </li>
                    @else
                        <li>
                            <a href="/account/profile"><i class="soap-icon-user circle"></i> Profile</a>
                        </li>
                    @endif
                </ul>
            </nav>

        </div>
    </div>
</header>
@section('headercontainer')
    <div class="page-title-container hidden-xs">
        <div class="container">
            <div class="page-title pull-left">
                <h2 class="entry-title">@yield('title')</h2>
            </div>
            <ul class="breadcrumbs pull-right">
                @yield('crumbs')
            </ul>
        </div>
    </div>
@show
