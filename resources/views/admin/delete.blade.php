@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/profile">PROFILE</a></li>
@endsection
@section('content')
    <div class="admin-profile-form tab-pane in active">
        {!! Form::open(array('action' => 'Admin\ProfileController@deleteAccount', 'class' => 'form')) !!}
        <div class="form-group row">
            <p>Are you sure you want to delete your account?</p>
            <div class="col-sm-3">

                <label>
                    <input type="radio" name="confirm" value="yes">
                    Yes
                </label>

            </div>
            <div class="col-sm-3">

                <label>
                    <input type="radio" name="confirm" checked="" value="no">
                    No
                </label>

            </div>

            <div class="clearfix"></div>
            {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
        </div>

        {!! Form::close() !!}
    </div>
@endsection