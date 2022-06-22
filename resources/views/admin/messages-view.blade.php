@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/users">Messages</a></li>
@endsection
@section('content')
@php
    $cpUrl = \Config::get('app.cp');
@endphp
<div class="users tab-pane in active">
    @if ($messages)
        @php
            $toUser = $messages[$length]['to'];
            $profileUrl = $toUser->getProfileUrl();
            $toContact = !empty($toUser->contactNumber)? "#" . $toUser->contactNumber : 'None';
            if ($profileUrl) {
                 $memberCategory = $toUser->getFieldValue('memberCategory');
                 $memberCategoryTitle = '';
                 if (!empty($memberCategory)) {
                     $memberCategoryTitle = $memberCategory[0]->title;
                 }
                 $toInfo = '<a target="_blank" href="' . $toUser->getProfileUrl() . '">' .
                  $toUser->name . " ($memberCategoryTitle)</a>";
            } else {
                 $toInfo = $toUser->name;
            }

            $fromUser = $messages[$length]['from'];
            $profileUrl = $fromUser->getProfileUrl();
            $fromContact = !empty($fromUser->contactNumber)? "#" . $fromUser->contactNumber : 'None';
            if ($profileUrl) {
              $fromInfo = '<a href="' . $fromUser->getProfileUrl() . '">'
              . $fromUser->name . '</a>';
            } else {
               $fromInfo = $fromUser->name;
            }
        @endphp


        @if (TemplateHelper::isUserRole('administrator')
            || TemplateHelper::isUserRole('superAdmin'))

                <h3 class="f-title text-center">
                    FROM:
                    {!! $fromInfo !!}
                </h3>
                <h3 class="f-title text-center">
                    TO:
                    {!! $toInfo !!}
                </h3>
            {!! Form::open(array('action' => ['Admin\QuotesController@store', $quoteId])) !!}
            <div class="box">
                {!! \Field::getInputHtmlByHandle('eventStatus') !!}

                    {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
                </div>
            {!! Form::close() !!}
        @endif
    {!! Form::open(array('url' => "/account/messages/send/$quoteId")) !!}
    <div class="panel panel-default">
        <div class="panel-body msg_container_base">
            @foreach($messages as $key => $message)

            <div class="row msg_container base_receive">
                <div class="col-md-2 col-xs-2 avatar">
                    @if (isset($message['fav']))
                    <div class="f-title">
                        <img src="{{ TemplateHelper::imageUrl($message['fav'][0]['url']) }}" class=" img-responsive ">
                    </div>
                    @endif
                    <h6 class="f-title text-center">
                        @php $profileUrl = $message['from']->getProfileUrl() @endphp
                        @if ($profileUrl)
                            <a target="_blank" href="{{ $message['from']->getProfileUrl() }}">{{ $message['from']->name }}</a>
                        @else
                            {{ $message['from']->name }}
                        @endif
                    </h6>
                </div>
                <div class="col-md-10 col-xs-10">
                    <div class="messages msg_receive">
                        <h4>{{ $message['messages']->title ?? '' }}</h4>
                        <div class="message-body">
                            @if (!empty($message['messages']->body))
                                {{ $message['messages']->body }}
                            @endif
                        </div>
                        @if ($length == $key)
                         <div>
                             {!! $quoteText !!}
                             <strong>Sent To:</strong> {!! $toInfo !!}
                         </div>
                        @endif
                        <time datetime="{{ $message['time'] }}">{{ $message['timeAgo'] }}</time>
                    </div>
                </div>
            </div>

            @endforeach
        </div>
        <div class="panel-footer">
            <div class="col-xs-1">
            </div>
            <div class="col-xs-6">
                <div class="input-group">
                    @if ($sendForm)
                    <div class="send-form box">

                        {!! $sendForm->displayHtml() !!}

                    </div>
                    @endif
                </div>
            </div>
            <div class="col-xs-5">
                @php
                    if ($currentUserId == $toUser->id) {
                        $contactName   = $fromInfo;
                        $contactNumber = $fromContact;
                    } else {
                        $contactName   = $toInfo;
                        $contactNumber = $toContact;
                    }
                @endphp
                <h4><small>Contact</small> {!! $contactName !!} <small> to negotiate service rates.</small></h4>
                <h4>{{ $contactNumber }}</h4>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    {!! Form::close() !!}
    @endif
</div>

@endsection
@section('header')
    <style>

        .col-md-2, .col-md-10{
            padding:0;
        }
        .panel{
            margin-bottom: 0px;
        }

        .chat-window > div > .panel{
            border-radius: 5px 5px 0 0;
        }

        .msg_container_base{
            background: #e5e5e5;
            margin: 0;
            padding: 10px;
            overflow-x:hidden;
        }

        .msg_receive{
            padding-left:0;
            margin-left:0;
        }

        .messages {
            background: white;
            padding: 10px;
            border-radius: 2px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            max-width:100%;
        }
        .messages > p {
            font-size: 13px;
            margin: 0 0 0.2rem 0;
        }
        .messages > time {
            font-size: 11px;
            color: #ccc;
        }
        .msg_container {
            padding: 0;
            overflow: hidden;
            display: flex;
        }
        img {
            display: block;
            width: 100%;
        }
        .avatar {
            position: relative;
            width: 100px;
        }

        .base_receive > .avatar:after {
            content: "";
            position: absolute;
            top: 0;
            right: 10px;
            width: 0;
            height: 0;
            border: 5px solid #FFF;
            border-left-color: rgba(0, 0, 0, 0);
            border-bottom-color: rgba(0, 0, 0, 0);
        }

        .base_sent > .avatar:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 10px;
            width: 0;
            height: 0;
            border: 5px solid white;
            border-right-color: transparent;
            border-top-color: transparent;
        }

        .msg_sent > time{
            float: right;
        }



        .msg_container_base::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #F5F5F5;
        }

        .msg_container_base::-webkit-scrollbar
        {
            width: 12px;
            background-color: #F5F5F5;
        }

        .msg_container_base::-webkit-scrollbar-thumb
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #555;
        }

        .panel-footer {
            padding-top: 20px
        }
    </style>
@endsection