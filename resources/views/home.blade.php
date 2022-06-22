@extends('layouts.app')

@section('metaTitle', 'Gigtrooper')
@section('metaDescription', 'GigTrooper is an event crew directory for all kinds of event service needs.')

@section('metaHeader')
    <meta property="og:locale" content="en_US"/>
    <meta property="og:type" content="website"/>
    <meta property="og:title"
          content="Gigtrooper is an event crew directory for all kinds of event service needs"/>
    <meta property="og:description"
          content="GigTrooper is an event crew directory for all kinds of event service needs."/>
    <meta property="og:url" content="{{ Config::get('app.url') }}"/>
    <meta property="og:site_name" content="{{ Config::get('app.name') }}"/>
    <meta property="og:image"
          content="https://s3-ap-southeast-1.amazonaws.com/gigtrooper/images/gigtrooper-logo.png"/>
    <meta property="og:image:alt" content="Gigtrooper logo"/>
@endsection

@section('content')
    <h1 class="home-title page-title text-center"><strong>Your event provider marketplace!</strong></h1>
    <br/>
    <div class="container section">
        <h2>Explore services for your event.</h2>
        <div class="row image-box style10">
            @if ($memberCategories)
                @foreach ($memberCategories as $memberCategory)
                    <div class="col-sms-6 col-sm-6 col-md-3">
                <article class="box">
                    <figure>
                        <a href="{{ $memberCategory->getUrl() }}" title="" class="hover-effect">
                            @php $memberImage = $memberCategory->getFieldValue('memberCategoryImage') @endphp
                            @if (!empty($memberImage))
                                @php
                                    $firstImage = TemplateHelper::imageUrl($memberImage[0]->url);
                                @endphp
                                <img src="{{ $firstImage }}" alt="" width="270" height="160" />
                            @else
                                <img src="http://placehold.it/270x160" alt="" width="270" height="160" />
                            @endif
                        </a>
                    </figure>
                    <div class="details">
                        <a href="{{ $memberCategory->getUrl() }}" class="button btn-mini">SEE ALL</a>
                        <h4 class="box-title">{{ $memberCategory->title }}</h4>
                    </div>
                </article>
            </div>
                @endforeach
            @endif
        </div>
    </div>

    @if ($recentPosts)

        <div class="row image-box listing-style1">
            <h2 class="text-center">Recent Posts</h2>
            @foreach ($recentPosts as $post)
                <div class="col-sm-6 col-md-3">
                    <article class="box">
                        @php
                            $first = \TemplateHelper::getFirstImageObj($post);
                        @endphp
                        @if ($first)
                            <figure>
                                <a href="/blog/{{ $post->slug }}">
                            <span>
                              <img alt="{{ $post->title }} image"
                                   src="{{ \TemplateHelper::imageUrl($first->getThumbUrl()) }}">
                            </span>
                                </a>
                            </figure>
                        @endif
                        <div class="details">
                            <h4 class="box-title">
                                <a href="/blog/{{ $post->slug }}">{{ $post->title }}</a>
                            </h4>

                            <div class="description">
                                {!! $post->subTitle !!}
                            </div>

                            <div class="action text-center">
                                <a class="button btn-small" href="/blog/{{ $post->slug }}">VIEW
                                    POST</a>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

    @endif
@endsection

@section('belowcontainer')
    <div class="section global-map-area">
        <div class="container description">
            <h1 class="text-center box">Why GigTrooper?</h1>
            <div class="row">
                <div class="col-xs-6 col-sm-3 box-info">
                    <div class="icon-box style8">
                        <i class="soap-icon-search"></i>
                        <h4 class="box-title">EASY SEARCHING</h4>
                        <p class="description">
                            GigTrooper is the easiest way to search for and hire various talented
                            performers and
                            services needed for a successful and remarkable event.
                        </p>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-3 box-info">
                    <div class="icon-box style8">
                        <i class="glyphicon glyphicon-filter"></i>
                        <h4 class="box-title">ADVANCED FILTERS</h4>
                        <p class="description">
                            It features advanced filters to meet your event requirements.
                        </p>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-3 box-info">
                    <div class="icon-box style8">
                        <i class="soap-icon-star-1"></i>
                        <h4 class="box-title">RECOMMENDED</h4>
                        <p class="description">
                            With various kinds of performers and services, GigTrooper has been
                            recommended,
                            For the convenience it offers and consummate searching.
                        </p>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-3 box-info">
                    <div class="icon-box style8">
                        <i class="soap-icon-insurance"></i>
                        <h4 class="box-title">OPTIONS</h4>
                        <p class="description">
                            With a wide variety of performers and services to choose from, you sure
                            are one step away to your dream event.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('header')
    <link href="{{ mix('css/ui-dropdown.css') }}" rel="stylesheet"/>
@endsection

@section('beforebody')
    <script type="text/javascript" src="{{ mix('js/ui-dropdown.js') }}"></script>
    <script type="text/javascript" src="/js/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript">

        jQuery(function($) {
            var CSRF_TOKEN = $("input[name=_token]").attr('value');

            $('.search-dropdown').dropdown({
                allowAdditions: true
            })
        });
    </script>
@endsection

@section('headercontainer')
    <div class="search-box-wrapper">
        <div class="search-box container">
            <div class="search-tab-content">
                <div class="tab-pane fade active in">
                    <div class="row">
                        {!! Form::open(array('action' => 'HomeController@homeSearch', 'class' => 'form')) !!}
                        <div class="col-sm-5">
                            <label>Searching for:</label>
                            {!! $categoryDropdown !!}
                        </div>

                        <div class="col-sm-3">
                            <label>Country</label>
                            {!! $countriesDropdown !!}
                        </div>

                        <div class="form-group col-sm-3">
                            <label class="hidden-xs">&nbsp;</label>
                            <button type="submit" class="btn-medium uppercase full-width">SEARCH
                                NOW
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection