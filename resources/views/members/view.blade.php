@extends('layouts.app')
@section('crumbs')
    <li><a href="/search/members/{{ \App::make('countryService')->getSessionCountry() }}/{{ \Request::segment(1)
    }}">SEARCH</a></li>
@endsection

@section('metaHeader')
    @php
        $slug = "";
        $categoryTitle = "";
        if ($category = $member->getFieldValueFirst('memberCategory'))
        {
            $slug = $category->slug;
            $categoryTitle = $category->title;
        }

    @endphp
    @php
        $firstImage = '';

        if ($member->getFieldValue('Avatar'))
        {
            $image = $member->getFieldValue('Avatar');

            $firstImage = TemplateHelper::imageUrl($image[0]->url);
        }

    @endphp
    <script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=594298a4f5d7e3001290fddb&product=sticky-share-buttons' async='async'></script>
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $member->name . " - " . $categoryTitle }}" />

    <meta property="og:description" content="{!! TemplateHelper::stripHtmlBreak($member->getFieldValue('introduction'))  !!}" />
    <meta property="og:url" content="{{ Config::get('app.url') }}/{{ $slug }}/{{ $member->slug }}" />
    <meta property="og:site_name" content="{{ Config::get('app.name') }}" />
    <meta property="og:image" content="{{ $firstImage }}" />
@endsection
@section('content')
    <div id="main" class="col-md-9">
        <div class="tab-container style1">
            @if ($member->getFieldValue('imageGallery'))
            <ul class="tabs">
                    <li class="active"><a data-toggle="tab" href="#photos-tab">photos</a></li>
            </ul>
            @endif
            <div class="tab-content">
                <div class="tab-pane fade in active">
                        <div id="links-gallery" class="text-center">
                            @foreach ($member->getFieldValue('imageGallery') as $key => $image)
                                <a href="{{ TemplateHelper::imageUrl($image->url) }}" title="{{ $member->name }} image gallery {{ $key }}">
                                    <img class="img-thumbnail" src="{{ TemplateHelper::imageUrl($image->getThumbUrl()) }}" alt="{{ $member->name }}
                                            thumb image {{
                                            $key }}"
                                    />
                                </a>
                            @endforeach
                        </div>

                        <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
                            <div class="slides"></div>
                            <h3 class="title"></h3>
                            <a class="prev">‹</a>
                            <a class="next">›</a>
                            <a class="close">×</a>
                            <a class="play-pause"></a>
                            <ol class="indicator"></ol>
                        </div>
                </div>
            </div>
        </div>

        <div id="cruise-features" class="tab-container">
            <ul class="tabs">
                <li class="active"><a href="#description" data-toggle="tab">Overview</a></li>
                <li><a href="#details" data-toggle="tab">Details</a></li>
                <li><a href="#social-media" data-toggle="tab">Social Media</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade in active" id="description">

                    <div class="long-description">
                        <h2>About {{ $member->name }}</h2>
                        {!! $member->aboutMe !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="details">
                    @if ($member->memberCategorySubFields)
                            @foreach ($member->memberCategorySubFields as $subField)
                                @if ($subField['value'])
                                <h3>{{ $subField['title'] }}:</h3>
                                <p>{!! $subField['value'] !!}</p>
                                @endif
                            @endforeach
                    @endif
                </div>
                <div class="tab-pane fade" id="social-media">
                    @php
                    $fb = $member->getFieldValue('facebookUrl');
                    @endphp
                    @if ($fb)
                        <div class="box-title">
                            <h4>
                                <small><i class="soap-icon-facebook"></i> Facebook</small>
                                <a href="{!! $fb !!}">{{ $fb }}</a>
                            </h4>
                        </div>
                    @endif
                    @php
                    $youtube = $member->getFieldValue('youtubeUrl');
                    @endphp
                    @if ($youtube)
                        <div class="box-title">
                            <h4>
                                <small><i class="soap-icon-youtube"></i> Youtube</small>
                                <a href="{!! $youtube !!}">{{ $youtube }}</a>
                            </h4>
                        </div>
                    @endif

                    @php
                    $twitter = $member->getFieldValue('twitterUrl');
                    @endphp
                    @if ($twitter)
                        <div class="box-title">
                            <h4>
                                <small><i class="soap-icon-twitter"></i> Twitter</small>
                                <a href="{!! $twitter !!}">{{ $twitter }}</a>
                            </h4>
                        </div>
                    @endif

                    @php
                    $instagram = $member->getFieldValue('instagramUrl');
                    @endphp
                    @if ($instagram)
                        <div class="box-title">
                            <h4>
                                <small><i class="soap-icon-instagram"></i> Instagram</small>
                                <a href="{!! $instagram !!}">{{ $instagram }}</a>
                            </h4>
                        </div>
                    @endif

                    @php
                      $linkedIn = $member->getFieldValue('linkedInUrl');
                    @endphp
                    @if ($linkedIn)
                        <div class="box-title">
                            <h4>
                                <small><i class="soap-icon-linkedin"></i> LinkedIn</small>
                                <a href="{!! $linkedIn !!}">{{ $linkedIn }}</a>
                            </h4>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <div class="sidebar col-md-3">
        <article class="detailed-logo">
            <figure>
                @php $avatar = $member->getFieldValue('Avatar') @endphp
                @if (!empty($avatar))
                    <span>
                        <img alt="{{ $member->getFieldValue('name') }} avatar"
                                class="lazy" data-src="{{ TemplateHelper::imageUrl($avatar[0]->url) }}" />
                    </span>
                @else
                    <span><img alt="default avatar" src="/images/default-avatar.jpg"></span>
                @endif
            </figure>
            <div class="details detailed-logo">
                <div class="box-title">
                    <h2>{{ $member->getFieldValue('name')  }}</h2>
                    @php $currencySign = '' @endphp
                    @if ($country)
                        @php
                            $currencySign = ($country->currency)? $country->currency : '';
                        @endphp
                        <small>{{ $country->title }}</small>

                        @if ($member->countrySubFields)
                            <div class="feedback clearfix">
                            @foreach ($member->countrySubFields as $subField)
                            <span>
                                <small class="pull-left">{{ $subField['title'] }}:</small>
                                <span class="pull-right">{{ $subField['value'] }}</span>
                            </span>
                            @endforeach
                            </div>
                        @endif
                    @endif
                    <div class="clearfix">
                        @php
                            $memberCategory = $member->getFieldValueFirst('memberCategory');
                        @endphp
                        @if ($memberCategory)
                            <small class="pull-left">Category:</small>
                            <span class="pull-right">{{ $memberCategory->title }}</span>
                        @endif
                    </div>
                </div>
                <span class="price clearfix">
                     @if ($member->getFieldValue('fee'))
                        <small class="pull-left">from</small>
                        <span class="pull-right">
                          {{ $currencySign }}  {{ $member->getFieldValue('fee') }}
                         </span>
                     @else
                          <span class="pull-right">
                                Contact for quotation
                          </span>
                     @endif
                 </span>
                <p class="description">
                    {!! $member->getFieldValue('introduction')  !!}
                </p>
            </div>
        </article>

        <h5><a href="/add-provider/{{ $member->id }}" class="button full-width uppercase btn-large green">REQUEST QUICK QUOTE</a></h5>
        <h3 class="text-center">Lowest Price Guaranteed<br /> <small>(No Comission Fee)</small></h3>
    </div>
@endsection

@section('beforebody')
    <script type="text/javascript" src="/jquery.bxslider/jquery.bxslider.min.js"></script>
    <script type="text/javascript" src="/flexslider/jquery.flexslider-min.js"></script>
    <script>
        document.getElementById('links-gallery').onclick = function (event) {
            event = event || window.event;
            var target = event.target || event.srcElement,
                link = target.src ? target.parentNode : target,
                options = {index: link, event: event},
                links = this.getElementsByTagName('a');
            blueimp.Gallery(links, options);
        };
    </script>
@endsection