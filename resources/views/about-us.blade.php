@extends('layouts.app')

@section('metaTitle', $page->title)
@section('metaDescription', strip_tags($page->excerpt))

@section('title')
    {{ $page->title }}
@endsection

@section('content')

    <div id="main">
        <div class="row travelo-box">
            <div class="col-sm-12">

                <div class="row">
                    <div class="col-sm-4">
                        <div class="mapouter"><div class="gmap_canvas"><iframe width="300" height="300" id="gmap_canvas" src="https://maps.google.com/maps?q=cordova%20cebu&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://www.jetzt-drucken-lassen.de">jetzt drucken lassen</a></div><style>.mapouter{text-align:right;height:300px;width:300px;}.gmap_canvas {overflow:hidden;background:none!important;height:300px;width:300px;}</style>Google Maps by <a href="https://www.embedgooglemap.net" rel="nofollow" target="_blank">Embedgooglemap.net</a></div>
                    </div>
                    <div class="col-sm-8">
                        <h3 class="pad-center">The location of the company is in Cordova, Cebu City, Philippines. Our services is available nationwide.</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('topcontainer')

        <div class="section skin-bg">
            <div class="container" style="color: #fff">
                @if ($page->excerpt)
                    <h2 style="color: #fff"><strong>WHAT IS GIGTROOPER?</strong></h2>
                    <p><strong>Imagine a place where everything that you need for an event meets, that’s us! GigTrooper is the marketplace for all kinds of event service needs. We also have entertaining blogs you can read on, if you just wish to visit our website.</strong></p>
                    <p>
                        <strong>Having made event planning easier and more secure with our number of trusted performers and professionals, our goal is solely for you to be able to create that extraordinary gathering/event you’ve always dreamed of with ease. Search for something awesome!</strong>
                    </p>
                @endif
            </div>
        </div>

@endsection

@section('belowcontainer')

        <div class="section skin-bg">
            <div class="container" style="color: #fff">
                @if ($page->excerpt)
                    <h2 style="color: #fff"><strong>VISION</strong></h2>
                    <p><strong>
                            The vision of the company is to help event providers "new or old" to get more Gigs.
                            GigTrooper also wants to give a hassle free and secure service to the clients.</strong></p>
                @endif
            </div>
        </div>

        <div class="section">
            <div class="container">
                <h2><strong>TEAM BEHIND GIGTROOPER</strong></h2>
                <div class="row">
                    <div class="col-sm-4 text-center">
                        <div class="box">
                            <img class="img-circle" src="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/thumbnail/Dale.jpg">
                        </div>
                        <h4><strong>Dale Ramirez</strong></h4>
                        <h3>Chief Technical Officer (CTO)</h3>

                    </div>
                    <div class="col-sm-4 text-center">
                        <div class="box">
                            <img class="img-circle" src="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/thumbnail/Tisha.jpg">
                        </div>
                        <h4><strong>Tisha Oppus</strong></h4>
                        <h3>Chief Operation Officer (COO)</h3>

                    </div>
                    <div class="col-sm-4 text-center">
                        <div class="box">
                            <img class="img-circle" src="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/thumbnail/Jhon.jpg">
                        </div>
                        <h4><strong>Jhonel Ramos</strong></h4>
                        <h3>Chief Marketing Officer (CMO)</h3>

                    </div>
                </div>
            </div>
        </div>

@endsection

@section('beforebody')
    @parent


@endsection

