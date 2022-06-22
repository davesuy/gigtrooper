@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/countries">Countries</a></li>
@endsection
@section('content')
    <div class="admin-create-post-form tab-pane in active">
        <h2>Create a Post</h2>

        @if (isset($element))
            {!! Form::open(array('action' => 'Admin\CountryController@store', 'class' => 'form')) !!}

		    {!! \Field::showInputHtml() !!}

            {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
            {!! Form::close() !!}
        @endif
    </div>

@endsection
