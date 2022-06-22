@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/countries">COUNTRIES</a></li>
@endsection
@section('content')
    @php $cpUrl = \Config::get('app.cp') @endphp
    <div class="countries tab-pane in active">
        <span>Query:</span> <br />

        <hr />
            <h2>Posts</h2>
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
                    <a href="/{{ $cpUrl }}/countries/create" class="button btn-medium">CREATE COUNTRY</a>
                </div>
            </div>

            <table class="table table-striped">
                {!! Form::open(array('url' => "$cpUrl/countries/deletes")) !!}

                <tr>
                    <th>
                        <input type="checkbox" name="countryselect" id="elementSelectAll" class="no-space" />
                    </th>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>CountryCode</th>
                    <th>Created</th>
                    <th>Published</th>
                </tr>
                @foreach($elements as $element)
                    <tr>
                        <td>
                            <input type="checkbox"
                                   name="ids[]"
                                   class="countryselect elementCheckbox no-space"
                                   value="{{ $element->id }}" />
                        </td>
                        <td>
                            {{ $element->id }}
                        </td>
                        <td class="s-title">
                            <a href="/{!! \Config::get('app.cp') !!}/countries/{{ $element->id  }}/edit">
                                {{ $element->title }}
                            </a>
                        </td>
                        <td>{{ $element->getFieldValue('slug') }}</td>
                        <td>{{ $element->getFieldValue('countryCode') }}</td>
                        <td>
                            @php
                            $time = $element->getFieldValue('dateCreated')
                            @endphp
                            {{ (!empty($time)) ? date('d-M-Y H:i', $time) : '' }}
                        </td>
                        <td>
                            @php
                            $time = $element->getFieldValue('DateTimePublished')
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
