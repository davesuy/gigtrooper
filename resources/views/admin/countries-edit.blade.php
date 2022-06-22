@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/countries">COUNTRIES</a></li>
@endsection
@section('content')
    <div class="admin-create-user-form tab-pane in active">
        <h2>Edit Post</h2>
        <div class="row">
            <div class="col-sm-offset-9 col-sm-3 text-right">
                <a target="_blank"
                        href="/blog/{{ $model->slug }}?preview=true"
                        class="button entry-comment btn-medium">PREVIEW</a>
            </div>
        </div>
        @if (isset($element))
        <?php $cpUrl = \Config::get('app.cp') ?>
        {!! Form::model($model, array('method' => 'put', 'action' => ["Admin\CountryController@update", $model->id],
            'class' =>
            'form')) !!}

          {!! \Field::showInputHtml() !!}

         {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
        {!! Form::close() !!}
        @endif

    </div>


@endsection
