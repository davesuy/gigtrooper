@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/categories">CATEGORIES</a></li>
@endsection
@section('content')
    <div class="admin-create-user-form tab-pane in active">
        <h2>Edit Category</h2>

        @if (isset($element))
        @php $cpUrl = \Config::get('app.cp') @endphp
        {!! Form::model($model, array('method' => 'put',
        'action' => ["Admin\MemberCategoryController@update", $model->id],
            'class' =>
            'form')) !!}

          {!! \Field::showInputHtml() !!}

          {{--{!! $subsHtml->displaySubsHtml() !!}--}}

            {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
        {!! Form::close() !!}
        @endif

    </div>

    <?php

    $queries = \Neo4jQuery::getQueryString();

    //echo nl2br($queries);
    ?>
@endsection

@section('header')
    <link href="{{ mix('css/ui-dropdown.css') }}" rel="stylesheet" />
@endsection

@section('beforebody')
    <script type="text/javascript" src="{{ mix('js/ui-dropdown.js') }}"></script>
@endsection
