@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/users">Messages</a></li>
@endsection
@section('content')
@php $cpUrl = \Config::get('app.cp') @endphp
<div class="users tab-pane in active">
    {!! Form::open(['action' => "Admin\QuotesController@filter"]) !!}
    <input type="hidden" name="currentUrl" value="{{ \Request::getRequestUri() }}" />
    <div class="box">
        <div class="form-group">
            {!! $search !!}
        </div>
        {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
    </div>
    {!! Form::close() !!}
    <div class="row">
        <div class="col-sm-2">
            <dl class="total-list term-description">
                <dt>Page:</dt> <dd>{{ $page }}</dd>
                <dt>Total:</dt> <dd>{{ $total }}</dd>
            </dl>
        </div>
    </div>
    @if ($messages)
    <table class="table table-striped">
        <tr>
            <th>
                Delete
            </th>
            <th>
                Title
            </th>
            <th>
                Last Message
            </th>
            <th>
                Date Sent
            </th>
            <th>
                From
            </th>
            <th>
                To
            </th>
            <th>
                Rate
            </th>
            <th>
                Accept Offer
            </th>
        </tr>
        {!! Form::open(array('url' => "$cpUrl/quotes/actions")) !!}
        @foreach($messages as $message)
            @php $messageUrl = "/$cpUrl/quotes/" . $message['quoteId']; @endphp
        <tr>
            <td>
                <input type="checkbox"
                       name="ids[]"
                       class="userSelect elementCheckbox no-space"
                       value="{{ $message['quoteId'] }}" />
            </td>
            <td>
                <a href="{{ $messageUrl }}">{{ $message['title'] }} ({{ $message['count'] }})</a>
            </td>
            <td>
                <a href="{{ $messageUrl }}">{{ $message['last'] }}</a>
            </td>
            <td>
                <a href="{{ $messageUrl }}">{{ $message['time'] }}</a>
            </td>
            <td>
                <a href="{{ $messageUrl }}">{{ $message['from']->name }}</a>
            </td>
            <td>
                <a href="{{ $messageUrl }}">{{ $message['to']->name }}</a>
            </td>
            <td>
                <a href="{{ $messageUrl }}">{{ $message['source']->offerFee }}</a>
            </td>
            <td>
                <a href="{{ $messageUrl }}">{{ $message['source']->acceptOffer }}</a>
            </td>
        </tr>
        @endforeach
        </table>
        {!! $pagination !!}
        <div class="form-group col-sm-2 no-float no-padding no-margin">
            {!! Form::button('DELETE', array('name' => 'delete', 'value' => 'delete', 'type' => 'submit', 'class' =>
            'btn-medium
            full-width' ))
             !!}
        </div>
        {!! Form::close() !!}
    @endif
</div>

@endsection
