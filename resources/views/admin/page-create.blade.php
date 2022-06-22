@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/users">PAGES</a></li>
@endsection
@section('content')
    <div class="admin-create-page-form tab-pane in active">
        <h2>Create Page</h2>

        @if (isset($element))
            {!! Form::open(array('action' => 'Admin\PageController@store', 'class' => 'form')) !!}

            {!! \Field::showInputHtml() !!}

            {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
            {!! Form::close() !!}
        @endif
    </div>

@endsection
