@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/member-categories">CATEGORIES</a></li>
@endsection
@section('content')
    <div class="categories tab-pane in active">

        <hr />

            <h2>Categories</h2>
            <div class="row">
                <div class="col-sm-2">
                    <dl class="total-list term-description">
                        <dt>Page:</dt> <dd>{{ $page }}</dd>
                        <dt>Total:</dt> <dd>{{ $total }}</dd>
                        <dt>Order:</dt> <dd></dd>
                    </dl>
                </div>
            </div>
        @php $cpUrl = \Config::get('app.cp') @endphp
        <div class="row">
            <div class="col-sm-4 col-sm-offset-8 text-right">
                <a href="/{{ $cpUrl }}/member-categories/create" class="button btn-medium">CREATE CATEGORY</a>
            </div>
        </div>

        {!! Form::open(array('url' => "$cpUrl/member-categories/deletes")) !!}

        {{--@if (!empty($categories))--}}
            {{--@foreach($categories as $category)--}}
                {{--<table>--}}
                    {{--<tr>--}}
                        {{--<td>--}}
                            {{--<div class="checkbox">--}}
                                {{--<input type="checkbox"--}}
                                        {{--name="ids[]"--}}
                                        {{--class="categoryelect elementCheckbox"--}}
                                        {{--value="{{ $category->id }}" />--}}
                            {{--</div>--}}
                        {{--</td>--}}
                        {{--<td><a class="s-title post-title"--}}
                               {{--href="/{!! \Config::get('app.cp') !!}/member-categories/{{ $category->id }}/edit">--}}
                                {{--{{ $category->title }}--}}
                            {{--</a></td>--}}
                    {{--</tr>--}}
                {{--</table>--}}
            {{--@endforeach--}}
        {{--@endif--}}
        <hr />
        {!! $menuTree !!}
        <hr />

        <div class="form-group col-sm-2 no-float no-padding no-margin">
            {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium full-width' )) !!}
        </div>
        {!! Form::close() !!}


        <br />
<?php

    $queries = \Neo4jQuery::getQueryString();

   // echo nl2br($queries);
?>

    </div>

@endsection
