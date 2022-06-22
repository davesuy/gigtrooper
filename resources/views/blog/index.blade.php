 @extends('layouts.app')

@section('metaTitle', 'Gigtrooper Blog')
@section('metaDescription', 'Gigtrooper blog posts.')

@section('title')
   @if (isset($category))
    Category: {{ $category->title }}
   @elseif (isset($tag))
    Tag: {{ ucwords($tag->value) }}
   @else
    Blog
   @endif
@endsection

@section('headercontainer')
    @parent
    <div class="row">
        <div class="col-sm-12 text-center">
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Top header -->
        <ins class="adsbygoogle"
                style="display:block"
                data-ad-client="ca-pub-4838720912538149"
                data-ad-slot="4271397717"
                data-ad-format="auto"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        </div>
    </div>
@endsection

@section('content')

        <div id="main" class="col-sm-8 col-md-9">
            <div class="page">
                <div class="post-content">
                    <div class="row image-box listing-style1">
                                @if (!empty($posts))
                                    @foreach ($posts as $post)
                                <div class="col-sm-6 col-md-4 post {{ (empty($image = $post->getFieldValue('Image'))) ?
                                'without-featured-item' : '' }}">
                                    <article class="box">
                                        @if (!empty($image))
                                            <figure class="image-container">
                                                <a href="/blog/{{ $post->slug }}" class="hover-effect">

                                                    <img src="{{ \TemplateHelper::getFirstImage($post) }}"
                                                            alt="" /></a>
                                            </figure>
                                        @endif
                                        <div class="details">
                                            <h2 class="entry-title">
                                                <a href="/blog/{{ $post->slug }}">{{ $post->title }}</a>
                                            </h2>
                                            <div class="excerpt-container">
                                                {{--{!! $post->excerpt !!}--}}
                                                <p><a class="button btn-small" href="/blog/{{ $post->slug }}">Read More</a></p>
                                                @php
                                                    $timeStamp = $post->getDate('DateTimePublished', false);
                                                    $postDates = [];

                                                    if (!empty($timeStamp)) {

                                                        $postDates['year'] = date("Y", $timeStamp);
                                                        $postDates['month'] = date("m", $timeStamp);
                                                        $postDates['day'] = date("d", $timeStamp);
                                                    }
                                                @endphp
                                            </div>
                                            <div class="post-meta">
                                                <div class="entry-date">
                                                    <label class="date">
                                                    {{
                                                    (!empty($postDates["day"]))? $postDates["day"] : ''
                                                    }}
                                                    </label>
                                                    <label class="month">
                                                        @php
                                                        if (!empty($postDates["month"]))
                                                        {
                                                          $time  = mktime(0, 0, 0, $postDates["month"]);
                                                          $month = strftime("%b", $time);
                                                          echo $month;
                                                        }
                                                        @endphp
                                                    </label>
                                                </div>
                                                {{--<div class="entry-author fn">--}}
                                                    {{--<i class="icon soap-icon-user"></i> Posted By:--}}
                                                    {{--<a href="#" class="author">--}}
                                                    {{--@php--}}
                                                        {{--$author = $post->getFieldValue('blogAuthor');--}}
                                                        {{--if ($author)--}}
                                                        {{--{--}}
                                                           {{--echo $author[0]->name;--}}
                                                        {{--}--}}

                                                        {{--$url = config('app.url') . '/blog/' . $post->slug;--}}
                                                     {{--@endphp--}}
                                                    {{--</a>--}}
                                                {{--</div>--}}
                                                {{--<div class="entry-action">--}}
                                                    {{--@if  ($post->getFieldValue('Tag') )--}}
                                                    {{--<span class="entry-tags"><i class="soap-icon-features"></i><span>--}}
                                                           {{--{!! $post->getFieldValue('Tag') !!}--}}
                                                    {{--@endif--}}
                                                {{--</div>--}}
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                    @endforeach
                                @endif

                    </div>
                  {!! $pagination !!}
                </div>
            </div>
        </div>
        @include('blog.sidebar')
    </div>
@endsection
