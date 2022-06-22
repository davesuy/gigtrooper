@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/pages">pages</a></li>
@endsection
@section('content')
    @php $cpUrl = \Config::get('app.cp') @endphp
    <div class="pages tab-pane in active">

        {{--@if(!empty($pages))--}}
            <h2>pages</h2>
            <div class="row">
                <div class="col-sm-2">
                    <dl class="total-list term-description">
                        <dt>Page:</dt> <dd>{{ $page }}</dd>
                        <dt>Total:</dt> <dd>{{ $total }}</dd>
                        <dt>Order:</dt> <dd></dd>
                    </dl>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-sm-offset-8 text-right">
                    <a href="/{{ $cpUrl }}/pages/create" class="button btn-medium">CREATE PAGE</a>
                </div>
            </div>

            <table class="table table-striped">
                {!! Form::open(array('url' => "$cpUrl/pages/deletes")) !!}

                <tr>
                    <th>

                    </th>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Created</th>
                </tr>
                @foreach($pageElements as $pageElement)
                    <tr>
                        <td>
                            <input type="checkbox"
                                   name="ids[]"
                                   class="pageselect elementCheckbox no-space"
                                   value="{{ $pageElement->id }}" />
                        </td>
                        <td>
                            {{ $pageElement->id }}
                        </td>
                        <td class="s-title">
                            <a href="/{!! \Config::get('app.cp') !!}/pages/{{ $pageElement->id  }}/edit">
                                {{ $pageElement->title }}
                            </a>
                        </td>
                        <td>{{ $pageElement->getFieldValue('slug') }}</td>
                        <td>
                            @php
                            $time = $pageElement->getFieldValue('dateCreated')
                            @endphp
                            {{ (!empty($time)) ? date('d-M-Y H:i', $time) : '' }}
                        </td>
                    </tr>
                @endforeach

                </table>
        {!! $pagination !!}
        <div class="form-group col-sm-2 no-float no-padding no-margin">
            {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium full-width' )) !!}
        </div>
        {!! Form::close() !!}

<br />
<?php

$queries = \Neo4jQuery::getQueryString();

//echo nl2br($queries);
?>
</div>

@endsection
