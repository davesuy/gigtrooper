@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/member-categories">CATEGORIES</a></li>
@endsection
@section('content')
    <div class="admin-create-category-form tab-pane in active">
        <h2>Create Member Category</h2>

        {{--{!! Form::open(array('route' => 'admin.user.store', 'class' => 'form')) !!}--}}
           {{----}}
        {{--{!! Form::close() !!}--}}
        @if (isset($element))
            {!! Form::open(array('action' => 'Admin\MemberCategoryController@store', 'class' => 'form')) !!}
            {!! \Field::showInputHtml() !!}
            {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
            {!! Form::close() !!}
        @endif
    </div>

@endsection
@section('header')
    <link href="{{ mix('css/ui-dropdown.css') }}" rel="stylesheet" />
@endsection

@section('beforebody')
    <script type="text/javascript" src="{{ mix('js/ui-dropdown.js') }}"></script>
@endsection