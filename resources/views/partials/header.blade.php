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
                                <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endif

            </ul>
        </div>
    </div>

    <div class="main-header">

        <a href="#mobile-menu-01" data-toggle="collapse" class="mobile-menu-toggle">
            Mobile Menu Toggle
        </a>

        <div class="container">
            <h1 class="logo navbar-brand">
                <a href="/" title="Gigtrooper - home">
                    {{ HTML::image('/images/logo.png', 'Gigtrooper logo') }}
                </a>
            </h1>

            @include('partials/nav')
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
