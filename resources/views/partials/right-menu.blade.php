<ul class="right">

    <li class="has-dropdown">
        @if (Auth::guest())
            <a href="#">Account</a>
            <ul class="dropdown">
                <li><a href="/login">Login</a></li>
                <li><a href="/register">Register</a></li>
            </ul>
        @else
            <a href="#">{{ Auth::user()->name }}</a>
		    @php $cpUrl = "/" . \Config::get('app.cp') @endphp
            <ul class="dropdown">
                <li><a href="{{ $cpUrl }}/dashboard">Dashboard</a></li>
                <li>
                    <a href="{{ url('/logout') }}" >Logout</a>
                </li>
            </ul>
        @endif
    </li>
</ul>