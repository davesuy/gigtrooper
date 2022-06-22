@extends('layouts.app')

@section('metaTitle', $page->title)
@section('metaDescription', strip_tags($page->excerpt))

@section('title')
    {{ $page->title }}
@endsection

@section('content')

    <div id="main">
        @if ($page->excerpt )
            <div class="row image-style style1 large-block">
                <h2>{{ $page->subTitle }}</h2>
                {!! $page->excerpt !!}
            </div>
        @endif
        @if ($page->slug == 'about-us')
            <div class="row">
                <div class="col-sm-12 travelo-box">
                    @include("partials/slideshow")
                </div>
            </div>
        @endif
        <div class="row travelo-box">
            <div class="col-sm-12">
                {!! $page->body !!}
            </div>
        </div>
    </div>
@endsection

@section('header')
    @parent

    @if ($page->slug == 'about-us')
        <link rel="stylesheet" type="text/css" href="components/revolution_slider/css/settings.css"
              media="screen"/>
        <link rel="stylesheet" type="text/css" href="components/revolution_slider/css/style.css"
              media="screen"/>
    @endif
@endsection

@section('beforebody')
    @parent

    @if ($page->slug == 'about-us')
        <script type="text/javascript"
                src="components/revolution_slider/js/jquery.themepunch.tools.min.js"></script>
        <script type="text/javascript"
                src="components/revolution_slider/js/jquery.themepunch.revolution.min.js"></script>

        <script type="text/javascript">
            tjq(document).ready(function() {
                tjq('.revolution-slider').revolution(
                    {
                        sliderType: "standard",
                        sliderLayout: "auto",
                        dottedOverlay: "none",
                        delay: 4000,
                        navigation: {
                            keyboardNavigation: "off",
                            keyboard_direction: "horizontal",
                            mouseScrollNavigation: "off",
                            mouseScrollReverse: "default",
                            onHoverStop: "on",
                            touch: {
                                touchenabled: "on",
                                swipe_threshold: 75,
                                swipe_min_touches: 1,
                                swipe_direction: "horizontal",
                                drag_block_vertical: false
                            }
                            ,
                            arrows: {
                                style: "default",
                                enable: true,
                                hide_onmobile: false,
                                hide_onleave: false,
                                tmp: '',
                                left: {
                                    h_align: "left",
                                    v_align: "center",
                                    h_offset: 20,
                                    v_offset: 0
                                },
                                right: {
                                    h_align: "right",
                                    v_align: "center",
                                    h_offset: 20,
                                    v_offset: 0
                                }
                            }
                        },
                        visibilityLevels: [1240, 1024, 778, 480],
                        gridwidth: 1170,
                        gridheight: 646,
                        lazyType: "none",
                        shadow: 0,
                        spinner: "spinner4",
                        stopLoop: "off",
                        stopAfterLoops: -1,
                        stopAtSlide: -1,
                        shuffle: "off",
                        autoHeight: "off",
                        hideThumbsOnMobile: "off",
                        hideSliderAtLimit: 0,
                        hideCaptionAtLimit: 0,
                        hideAllCaptionAtLilmit: 0,
                        debugMode: false,
                        fallbacks: {
                            simplifyAll: "off",
                            nextSlideOnWindowFocus: "off",
                            disableFocusListener: false,
                        }
                    });
            });
        </script>
    @endif
@endsection

