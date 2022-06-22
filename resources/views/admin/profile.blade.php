    @extends('admin.layouts.app')
    @section('crumbs')
    <li><a href="/account/profile">PROFILE</a></li>
    @endsection
    @section('content')
    <div class="admin-profile-form tab-pane in active">
        @php
                $slug = "";
                $categoryTitle = "";
                if ($category = $model->getFieldValueFirst('memberCategory'))
                {
                    $slug = $category->slug;
                    $categoryTitle = $category->title;
                }

                $profileUrl = "";
                if ($model->slug && $slug)
                {
                    $profileUrl = '/' . $slug . '/' . $model->slug;
                }
            @endphp
            <div id="fb-root"></div>
            <script>(function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)){return;
    }
                    js = d.createElement(s);
                    js.id = id;
                    js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10&appId=471033783229489';
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>
            <h2>Profile
                <small>{!!
            ($profileUrl)? ' - <a target="_blank" href="' . $profileUrl .
            '">View your profile</a>'
            : ''
             !!}</small>
            </h2>
            <div class="text-center">
                <div class="fb-share-button"
                     data-href="{{ Config::get('app.url') . $profileUrl }}"
                     data-layout="button_count" data-size="small" data-mobile-iframe="true"><a
                            class="fb-xfbml-parse-ignore" target="_blank"
                            href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse">Share</a>
                </div>
            </div>

            @php
                $errorKeys = [];
                $active = 'active';
                $info = $active;
                $career = '';
                if (count($errors) > 0)
                {
                    if ($errors->has('fields.introduction'))
                    {
                         $info = '';
                         $career = $active;
                    }
                }

            @endphp


            @if (isset($element))
                {!! Form::model($model, array(
                    'method' => 'put',
                    'action' => ["Admin\ProfileController@update"],
                    'class' => 'form')) !!}
                <div class="tab-container style1">
                    <ul class="tabs-links">
                        <li class="{{ $info }}"><a href="#profile-info" data-toggle="tab">Info</a></li>
                        <li class="{{ $career }}"><a href="#profile-career" data-toggle="tab">More
                                Info</a></li>
                        <li><a href="#profile-account" data-toggle="tab">Account</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in {{ $info }}" id="profile-info">
                            {!! \Field::getInputHtmlByHandle('name') !!}
                            {!! \Field::getInputHtmlByHandle('Avatar') !!}
                            {!! \Field::getInputHtmlByHandle('memberCategory') !!}

                            <div id="subfields-content-memberCategory"
                                    {{ ($memberCategoryId)? "data-category-id=$memberCategoryId" : '' }}>
                                <div style="margin: 0 auto" class="hide ajax loading center"></div>
                                <div class="content">
                                    {!! $subsHtmlDisplay !!}
                                </div>
                            </div>
                            {!! \Field::getInputHtmlByHandle('imageGallery') !!}
                            {!! \Field::getInputHtmlByHandle('contactNumber') !!}
                            {!! \Field::getInputHtmlByHandle('Country') !!}

                            <div id="subfields-content-Country"
                                    {{ ($countryId)? "data-category-id=$countryId" : '' }}>
                                <div style="margin: 0 auto" class="hide ajax loading center"></div>
                                <div class="content">
                                    {!! $subsHtmlDisplayRegion !!}
                                </div>
                            </div>
                            {!! \Field::getInputHtmlByHandle('facebookUrl') !!}
                            {!! \Field::getInputHtmlByHandle('youtubeUrl') !!}
                            {!! \Field::getInputHtmlByHandle('twitterUrl') !!}
                            {!! \Field::getInputHtmlByHandle('instagramUrl') !!}
                            {!! \Field::getInputHtmlByHandle('linkedInUrl') !!}
                        </div>
                        <div class="tab-pane fade in {{ $career }}" id="profile-career">
                            {!! \Field::getInputHtmlByHandle('introduction') !!}
                            {!! \Field::getInputHtmlByHandle('aboutMe') !!}
                            <h3 class="text-center">{{ $currency }}</h3>
                            {!! \Field::getInputHtmlByHandle('fee') !!}
                        </div>
                        <div class="tab-pane fade" id="profile-account">
                            {!! \Field::getInputHtmlByHandle('email') !!}
                            {!! \Field::getInputHtmlByHandle('password') !!}
                            <p><a style="text-decoration: underline" href="/account/profile/delete">Delete
                                    Account</a></p>
                        </div>

                        <div style="padding: 20px">
                            {!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

            @endif
        </div>
    @endsection

    @section('header')
        <link href="{{ mix('css/ui-dropdown.css') }}" rel="stylesheet"/>
    @endsection

    @section('beforebody')
        <script type="text/javascript" src="{{ mix('js/ui-dropdown.js') }}"></script>
        <script type="text/javascript" src="/js/jquery.ui.touch-punch.min.js"></script>

{{--        @if (!$model->getAttribute('shareBox') &&
        $model->getFieldValue('name') &&
        $model->getFieldValue('Country') &&
        $model->getFieldValue('memberCategory')
        )

            <div id="sharebox">
                <h3>Promote your profile now by sharing it.</h3>
                <div class="fb-share-button"
                     data-href="{{ Config::get('app.url') . $profileUrl }}"
                     data-layout="button_count" data-size="small" data-mobile-iframe="true"><a
                            class="fb-xfbml-parse-ignore" target="_blank"
                            href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse">Share</a>
                </div>
            </div>

            <script>
                jQuery(function() {
                    jQuery("#sharebox").modal();
                });

                jQuery('#sharebox').on(jQuery.modal.BEFORE_CLOSE, function(event, modal) {

                    var CSRF_TOKEN = jQuery("input[name=_token]").attr('value');
                    var data = {id: {{ $model->id }}, _token: CSRF_TOKEN};

                    jQuery.ajax({
                        url: '/profile/sharebox',
                        type: 'POST',
                        data: data,
                        success: function(data) {
                            console.log('done delete');
                        }
                    });
                });
            </script>
        @endif--}}
    @endsection