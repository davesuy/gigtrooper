@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/users">USERS</a></li>
@endsection
@section('content')
    @php $cpUrl = \Config::get('app.cp') @endphp
    <div class="users tab-pane in active">
        {{--<span>Query:</span> <br />--}}
        {{--{!! nl2br($query) !!}--}}
        <hr />
        {{--@if(!empty($users))--}}
            <h2>Users</h2>
            <div class="row">
                <div class="col-sm-2">
                    <dl class="total-list term-description">
                        <dt>Page:</dt> <dd>{{ $page }}</dd>
                        <dt>Total:</dt> <dd>{{ $total }}</dd>
                        <dt>Order:</dt> <dd></dd>
                    </dl>
                </div>
                <div class="col-sm-4">
                    <div class="filterBy">
                        {!! Form::open(array('id' => 'filterForm', 'url' => $baseUrl . 'filterElements')) !!}
                        <input type="hidden" name="currentUrl" value="{{ \Request::getRequestUri() }}" />
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="full-width">
                                    {!! $filters['Role'] !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="full-width">
                                    {!! $filters['Status'] !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="full-width">
                                    {!! $filters['name'] !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="full-width">
                                    {!! $subsHtmlDisplay !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-5 no-float no-padding no-margin">
                            {!! Form::button('Filter', array('type' => 'submit', 'class' => 'btn-medium full-width' ))
                             !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                <div class="col-sm-4 end">
                    {!! Form::open(array('id' => 'sortForm', 'url' => $baseUrl . 'sortElements')) !!}
                        <input type="hidden" name="currentUrl" value="{{ \Request::getRequestUri() }}" />
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="full-width">
                                <label>Sort Category</label>
                                {!! $sorts['category'] !!}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="full-width">
                                <label>Sort Element</label>
                                {!! $sorts['element'] !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-sm-5 no-float no-padding no-margin">
                        {!! Form::button('Sort', array('type' => 'submit', 'class' => 'btn-medium full-width' ))
                         !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-sm-offset-8 text-right">
                    @php
                        $user = \Auth::getUser();

                        if (in_array('superAdmin', $user->roles)) {
                    @endphp
                    <a href="/{{ $cpUrl }}/users/create" class="button btn-medium">CREATE USER</a>
                    @php
                     }
                    @endphp
                </div>
            </div>

            <table class="table table-striped">
                {!! Form::open(array('url' => "$cpUrl/users/actions")) !!}

                <tr>
                    <th>

                    </th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Fee</th>
                    <th>Category</th>
                    <th>Login</th>
                    <th>APoints</th>
                    <th>Points</th>
                    <th>Login As</th>
                    <th>Date Created</th>
                    <th>Date Updated</th>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox"
                                   name="ids[]"
                                   class="userSelect elementCheckbox no-space"
                                   value="{{ $user->id }}" />
                        </td>
                        <td>
                            <a href="/{!! \Config::get('app.cp') !!}/users/{{ $user->id  }}/edit">{{ $user->id }}</a>
                        </td>
                        <td class="s-title">
                            <a href="/{!! \Config::get('app.cp') !!}/users/{{ $user->id  }}/edit">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td>{{ $user->slug }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->getFieldValue('Role') }}</td>
                        <td>{{ $user->getFieldValue('Status') }}</td>
                        <td>{{ $user->getFieldValue('fee') }}</td>
                        <td>
                            {{ ($memberCategory = $user->getFieldValue('memberCategory'))? $memberCategory[0]->title :
                            '' }}
                        </td>
                        <td>
                            {{ $user->loginMethod }}
                        </td>
                        <td>
                            {{ $user->adminPoints }}
                        </td>
                        <td>
                            {{ $user->points }}
                        </td>
                        <td>
                            <a href="/account/login-as-user/{{ $user->id  }}">Login</a>
                        </td>

                        <td>
                            @php
                            $time = $user->getFieldValue('dateCreated');

                            @endphp
                            {{ (!empty($time)) ? \App::make('dateService')->getDateByFormat($time) : '' }}
                        </td>

                        <td>
                            @php
                            $time = $user->getFieldValue('dateUpdated');

                            @endphp
                            {{ (!empty($time)) ? \App::make('dateService')->getDateByFormat($time) : '' }}
                        </td>
                    </tr>
                @endforeach

                </table>
        {!! $pagination !!}
        @php
        $status = request('f.Status');

        if (isset($status) && $status == 'disabled')
        {
        @endphp
        <div class="form-group col-sm-2 no-float no-padding no-margin">
            {!! Form::button('DELETE', array('name' => 'delete', 'value' => 'delete', 'type' => 'submit', 'class' =>
            'btn-medium
            full-width' ))
             !!}
        </div>
        @php
        }
        @endphp
        {!! Form::close() !!}

<br />
<?php

//$queries = \Neo4jQuery::getQueryString();
//
//echo nl2br($queries);
?>
</div>

@endsection
