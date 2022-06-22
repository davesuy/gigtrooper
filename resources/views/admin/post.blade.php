@extends('admin.layouts.app')
@section('crumbs')
    <li><a href="/{{ \Config::get('app.cp') }}/blog">BLOG</a></li>
@endsection
@section('content')
    @php $cpUrl = \Config::get('app.cp') @endphp
    <div class="blog tab-pane in active">
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
                    <a href="/{{ $cpUrl }}/blog/create" class="button btn-medium">CREATE POST</a>
                </div>
            </div>

            <table class="table table-striped">
                {!! Form::open(array('url' => "$cpUrl/blog/deletes")) !!}

                <tr>
                    <th>

                    </th>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Category</th>
                    <th>Created</th>
                    <th>Published</th>
                </tr>
                @foreach($postElements as $postElement)
                    <tr>
                        <td>
                            <input type="checkbox"
                                   name="ids[]"
                                   class="blogselect elementCheckbox no-space"
                                   value="{{ $postElement->id }}" />
                        </td>
                        <td>
                            {{ $postElement->id }}
                        </td>
                        <td class="s-title">
                            <a href="/{!! \Config::get('app.cp') !!}/blog/{{ $postElement->id  }}/edit">
                                {{ $postElement->title }}
                            </a>
                        </td>
                        <td>{{ $postElement->getFieldValue('slug') }}</td>
                        <td>{{ (!empty($category = $postElement->getFieldValue('Category'))? $category[0]->title : '')
                        }}</td>
                        <td>
                            @php
                            $time = $postElement->getFieldValue('dateCreated')
                            @endphp
                            {{ (!empty($time)) ? date('d-M-Y H:i', $time) : '' }}
                        </td>
                        <td>
                            @php
                            $time = $postElement->getFieldValue('DateTimePublished')
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
</div>

@endsection
