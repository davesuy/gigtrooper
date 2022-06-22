@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/blog">Blog</a></li>
@endsection
@section('content')
    <div class="admin-create-post-form tab-pane in active">
        <h2>Create a Post</h2>

        @if (isset($element))
            {!! Form::open(array('action' => 'Admin\BlogController@store', 'class' => 'form')) !!}

            {!! \Field::showInputHtml() !!}

            {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
            {!! Form::close() !!}
        @endif
    </div>

@endsection

@section('header')
    <link href="{{ mix('css/ui-dropdown.css') }}" rel="stylesheet"/>
@endsection

@section('beforebody')
    <script type="text/javascript" src="{{ mix('js/ui-dropdown.js') }}"></script>
    <script type="text/javascript" src="/js/jquery.ui.touch-punch.min.js"></script>
@endsection