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
                    <div class="col-sm-6 box col-md-3">
                        <div class="work-box">
                            <a data-gallery="" download="Search and compare.png" href="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/Search%20and%20compare.png" title="Search and compare.png"><img src="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/thumbnail/Search%20and%20compare.png" /></a>
                        </div>

                        <h3><strong>BROWSE AND COMPARE</strong></h3>

                        <p>Compare rates and availability of local entertainers and event providers that you think is perfect for your event.</p>
                    </div>

                    <div class="col-sm-6 box col-md-3">
                        <div class="work-box">
                            <a data-gallery="" download="Request Quick Quote.png" href="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/Request%20Quick%20Quote.png" title="Request Quick Quote.png"><img src="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/thumbnail/Request%20Quick%20Quote.png" /></a>
                        </div>
                        <h3><strong>REQUEST QUICK QUOTE</strong></h3>

                        <p>Book online with our request quick quote feature and don&#39;t forget to fill everything correctly for faster results.</p>
                    </div>

                    <div class="col-sm-6 box col-md-3">
                        <div class="work-box">
                            <a data-gallery="" download="Contract.png" href="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/Contract.png" title="Contract.png"><img src="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/thumbnail/Contract.png" /></a>
                        </div>

                        <h3><strong>CONTRACT SIGNING</strong></h3>

                        <p>GigTrooper, client and provider will have a contract signing through emails for the security and assurance of the event.</p>
                    </div>

                    <div class="col-sm-6 box col-md-3">
                        <div class="work-box">
                            <a data-gallery="" download="Enjoy.png" href="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/Enjoy.png" title="Enjoy.png"><img src="https://gigtrooper.s3.amazonaws.com/staging/page/13/Image/thumbnail/Enjoy.png" /></a>
                        </div>

                        <h3><strong>ENJOY THE EVENT</strong></h3>

                        <p>Sit back, relax and enjoy your party.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="text-center">
                            <h2><strong>NO COMMISSION, ADMIN FEE ONLY!</strong></h2>
                            <h4>(The provider ONLY will pay the for the admin fee)</h4>
                            <table class="table-center">
                                <tr>
                                    <th>Service Fee</th>
                                    <th>Admin Fee</th>
                                </tr>
                                <tr>
                                    <td>₱10,000 - below</td>
                                    <td>₱200</td>
                                </tr>
                                <tr>
                                    <td>₱10,000 - ₱20,000</td>
                                    <td>₱400</td>
                                </tr>
                                <tr>
                                    <td>₱20,000 - up</td>
                                    <td>₱500</td>
                                </tr>
                            </table>
                            <div class="box">
                                <a href="/search/members/all/all" class="button btn-large green">GET STARTED</a>
                            </div>
                        </div>
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
                    <h1 style="color: #fff"><strong>How It Works</strong></h1>
                    <p><strong>{!! $page->excerpt !!}</strong></p>
                @endif
            </div>
        </div>

@endsection

@section('beforebody')
    @parent


@endsection

