@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/users">Messages</a></li>
@endsection
@section('content')
@php $cpUrl = \Config::get('app.cp') @endphp
<div class="users tab-pane in active">
    @if ($messages)
    <table class="table table-striped">
        {!! Form::open(array('url' => "$cpUrl/users/actions")) !!}
        @foreach($messages as $message)
            @php $messageUrl = "/account/messages/" . $message['quoteId']; @endphp
        <tr>
            <td>
                <a href="{{ $messageUrl }}">{{ $message['title'] }} to {{ $message['to']->name }} ({{ $message['count'] }})</a>
            </td>
            <td>
                <a href="{{ $messageUrl }}">{{ $message['last'] }}</a>
            </td>
            <td>
                <a href="{{ $messageUrl }}">{{ $message['time'] }}</a>
            </td>
        </tr>
        @endforeach
        {!! Form::close() !!}
     </table>
    @endif
</div>

@endsection
