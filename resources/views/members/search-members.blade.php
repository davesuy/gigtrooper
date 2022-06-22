@extends('layouts.app')
@section('crumbs')
    <li><a href="/search/members/">MEMBERS</a></li>
@endsection
@php $metaTitle = TemplateHelper::getMetaText() @endphp
@section('metaTitle', $metaTitle)
@php
    $metaDescription = "";
    if ($memberCategoryModel != null) {
        if ($memberCategoryModel->body != null) {
            $metaDescription = strip_tags($memberCategoryModel->body);
        }
    }
@endphp
@section('metaDescription', $metaDescription)
@section('content')
    <div id="main">
        <div class="row">
            @include('partials/nav-filters')
            <div class="col-sm-8 col-md-9">
                <div class="sort-by-section clearfix box">
                    {!! Form::open(array(
                    'action' => 'SearchMembersController@sortElements',
                    'class' => 'form')) !!}
                    <input type="hidden" name="currentUrl" value="{{ \Request::getRequestUri() }}" />

                    <ul class="sort-bar clearfix block-sm">
                        <li>
                            <h4 class="sort-by-title block-sm">
                                Sort results by:</h4>
                        </li>
                        <li class="sort-by-registered">
                            <strong>Date Registered</strong>
                            {!! $sorts['registered'] !!}
                        </li>

                        <li class="sort-by-fee">
                            <strong>Fee</strong>
                            {!! $sorts['fee'] !!}

                        </li>
                        <li>
                            <span>&nbsp;</span>
                            {!! Form::button('Sort',
                                array(
                                'type' => 'submit',
                                'style' => 'padding: 0 10px',
                                'class' => 'btn-medium uppercase full-width')
                                )
                            !!}
                        </li>
                    </ul>
                    {!! Form::close() !!}
                </div>
                <div class="search-list">
                    {{--{{ $query }}--}}
                    <div class="row image-box listing-style1">
                    @if ($members)
                        @foreach ($members as $key => $member)
                        <div class="col-sm-6 col-md-4">
                            <article class="box">
                            <figure>
                                @php $avatar = $member->getFieldValue('Avatar') @endphp
                                @if (!empty($avatar))
                                    <span>
                                        <img alt="{{ $member->getFieldValue('name') }} avatar"
                                                class="lazy"
                                                data-src="{{ TemplateHelper::imageUrl($avatar[0]->url) }}">
                                    </span>
                                @else
                                    <span><img alt="default avatar" class="lazy"
                                                data-src="/images/default-avatar.jpg"></span>
                                @endif
                            </figure>
                            <div class="details">
                                @php
                                    $country      = $member->getFieldValueFirst('Country');
                                    $currencySign = ($country)? $country->currency : '';
                                @endphp
                                <span class="price"><small>Fee Range: </small>
                                    @if ($member->getFieldValue('fee'))
                                        {{ $currencySign }} {{ $member->getFieldValue('fee') }}
                                    @else
                                        {{ "Contact for quotation" }}
                                    @endif
                                </span>
                                <div class="clearfix"></div>
                                <h4 class="box-title">{{ $member->getFieldValue('name') }}
                                    @if ($country)
                                        <small><strong>{{ $country->title
                                        }}</strong></small>
                                        @if ($member->countrySubFields)
                                            @foreach ($member->countrySubFields as $subField)
                                                <small> {{ $subField['value'] }}</small>
                                            @endforeach
                                        @endif
                                    @endif
                                </h4>
                                @php
                                    $slug = "";
                                    $categoryTitle = "";
                                    if ($category = $member->getFieldValueFirst('memberCategory'))
                                    {
                                        $slug = $category->slug;
                                        $categoryTitle = $category->title;
                                    }
                                @endphp
                                <h5 class="text-center"><strong>{{ $categoryTitle }}</strong></h5>
                                <p class="description">
                                    {{ TemplateHelper::stripHtmlBreak($member->getFieldValue('introduction')) }}
                                </p>

                                <div class="action box-title text-center">
                                    <a class="button btn-small" href="/{{ $slug }}/{{ $member->slug }}">VIEW PROFILE</a>
                                </div>
                                <div class="action text-center">
                                    <a class="button btn-small" href="/add-provider/{{ $member->id }}">REQUEST QUICK QUOTE</a>
                                </div>
                            </div>
                        </article>
                        </div>
                        @endforeach
                    @else
                        <div class="col-sm-12">
                            <div class="image-style style1 large-block">
                                <p>There are no members in {{ $currentMemberCategory }} yet.
                                    Be the first to <a style='text-decoration: underline'
                                            href="/register">Join</a> in this category.</p>
                            </div>
                        </div>
                    @endif

                </div>
                </div>
                {!! $pagination !!}
                <h4 clas="text-center">{{ $metaTitle }}</h4>
                @php
                    $categoryBody = "";
                    if ($memberCategoryModel != null) {
                        if ($memberCategoryModel->body != null) {
                            $categoryBody = $memberCategoryModel->body;
                        }
                    }
                @endphp
                {!! $categoryBody !!}
            </div>
        </div>
    </div>
@endsection
