@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/users">USERS</a></li>
@endsection
@section('content')
    <div class="admin-create-user-form tab-pane in active">
        <h2>Edit User</h2>

        @if (isset($element))
        <?php $cpUrl = \Config::get('app.cp') ?>
        {!! Form::model($model, array('method' => 'put', 'action' => ["Admin\UserController@update", $model->id],
            'class' =>
            'form')) !!}

          {!! \Field::showInputHtml() !!}

            <div id="subfields-content-Country"
                    {{ ($countryId)? "data-category-id=$countryId" : '' }}>
                <div style="margin: 0 auto" class="hide ajax loading center"></div>
                <div class="content">
                    {!! $subsHtmlDisplayRegion !!}
                </div>
            </div>

            <div id="subfields-content-memberCategory"
                    {{ ($memberCategoryId)? "data-category-id=$memberCategoryId" : '' }}>
                <div style="margin: 0 auto" class="hide ajax loading center"></div>
                <div class="content">
                    {!! $subsHtmlDisplay !!}
                </div>
            </div>

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
    <script type="text/javascript" src="/js/jquery.ui.touch-punch.min.js"></script>
@endsection