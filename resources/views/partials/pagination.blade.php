@if(!empty($links))
    <div class="pagination-left">
        <ul class="pagination">
            <li class="arrow {{ ($links['current'] == 1)? 'active' : '' }}">
                <a href="{{ $links['base'] . $links['first'] . $links['query_string'] }}">First</a></li>
            @if(isset($links['previous']))
                <li class="arrow"><a href="{{ $links['base'] . $links['previous']. $links['query_string'] }}">&laquo; Previous</a></li>
            @endif
            @if($links['pages'])

                @foreach($links['pages'] as $page)
                    @if($page == 'more')
                        <li class="unavailable"><a >&hellip;</a></li>
                    @elseif($page == $links['current'])
                        <li class="current active"><a>{{ $page }}</a></li>
                    @else
                        <li><a href="{{ $links['base'] . $page . $links['query_string'] }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif

            @if(isset($links['next']))
                <li class="arrow"><a href="{{ $links['base'] . $links['next'] . $links['query_string'] }}">Next &raquo;</a></li>
            @endif
            <li class="arrow {{ ($links['current'] ==  $links['last'])? 'active' : '' }}">
                <a href="{{ $links['base'] . $links['last'] . $links['query_string'] }}">Last</a></li>
        </ul>
    </div>
@endif