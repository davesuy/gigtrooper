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
<nav id="mobile-menu-01" class="mobile-menu collapse">
    <ul id="mobile-primary-menu" class="menu">
        <li>
            <a href="/how-it-works">HOW IT WORKS</a>
        </li>
        <li class="menu-item-has-children">
            <a href="/search/members/all/all">SEARCH</a>
            {!! $getMemberCategoryMenu !!}
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
</nav>