@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/pages">PAGES</a></li>
@endsection
@section('content')
    <div class="admin-create-user-form tab-pane in active">
        <h2>Edit Pages</h2>

        @if (isset($element))
        <?php $cpUrl = \Config::get('app.cp') ?>
        {!! Form::model($model, array('method' => 'put', 'action' => ["Admin\PageController@update", $model->id],
            'class' =>
            'form')) !!}

          {!! \Field::showInputHtml() !!}

         {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
        {!! Form::close() !!}
        @endif

    </div>


@endsection
