@extends('layouts.app')

@section('metaTitle', 'Request Quick Quote')

@section('title')
    Request Quick Quote
@endsection
{{--@section('metaHeader')--}}
    {{--@parent--}}
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">--}}
{{--@endsection--}}
@section('stylesheets')
    @parent
    <link href="/smartwizard/css/smart_wizard.css" rel="stylesheet">
    <link href="/smartwizard/css/smart_wizard_theme_arrows.css" rel="stylesheet">
    <style type="text/css">
        .sw-main .sw-container {
            overflow: visible;
        }

        .sw-theme-arrows .step-content {
            padding: 20px;
        }
    </style>

@endsection
@section('content')

    @if ($providers)

        <div class="row image-box listing-style1">
            <h2 class="text-center">Request Quote</h2>

            @foreach ($providers as $member)
                <div class="col-sm-6 col-md-3 provider-request" id="provider-{{ $member->id }}">
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
                            <h4 class="box-title">
                                <a href="/blog/{{ $member->slug }}">{{ $member->name }}</a>
                            </h4>

                            <div class="action text-center">
                                <div class="f-title">
                                    <a class="button btn-small" href="/blog/{{ $member->slug }}">VIEW PROFILE</a>
                                </div>
                                <div class="f-title">
                                    <a class="close-button remove-provider" data-provider-id="{{ $member->id }}" href="/{{ $member->id }}">
                                        <i class="soap-icon-close"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

    @endif
    <div class="row">
        <div class="col-sm-12">
            <div class="travelo-box">
                <form action="/request-quote/quote" id="step-form"
                      role="form"
                      data-toggle="validator"
                      method="post" accept-charset="utf-8">
                        {{ csrf_field() }}
                        <div id="smartwizard">
                            <ul>
                                <li><a href="#step-1">Step 1</a></li>
                                <li><a href="#step-2">Step 2</a></li>
                                <li><a href="#step-3">Step 3</a></li>
                                @if (!$isLoggedIn)
                                    <li><a href="#step-4">Step 4</a></li>
                                <li><a href="#step-5">Step 5</a></li>
                                @endif
                            </ul>

                            <div>
                                <div id="step-1">
                                    <div id="form-step-0" role="form" data-toggle="validator">
                                        <h2>What kind of event are you planning?</h2>
                                        {!! \Field::getInputHtmlByHandle('eventType') !!}

                                        <h2>When is the event?</h2>
                                        {!! \Field::getInputHtmlByHandle('eventDate') !!}

                                    </div>
                                </div>
                                <div id="step-2">
                                    <div id="form-step-1" role="form" data-toggle="validator">
                                        <div class="form-group">
                                            <h2>Where is the location of your event?</h2>
                                            <input type="text" class="input-text full-width"
                                                   name="fields[eventLocation]" required
                                                   value="{{ old('fields.eventLocation') }}" />

                                            <div class="help-block with-errors"></div>
                                        </div>

                                        <h2>What time does your event start?</h2>
                                        {!! \Field::getInputHtmlByHandle('eventStartTime') !!}

                                    </div>
                                </div>
                                <div id="step-3">
                                    <div id="form-step-2" role="form" data-toggle="validator">

                                        <h2>How long do you need the service?</h2>
                                        {!! \Field::getInputHtmlByHandle('eventServiceLength') !!}

                                        <h2>How many guests are you expecting?</h2>
                                        {!! \Field::getInputHtmlByHandle('eventGuests') !!}

                                        <h2>Additional Details</h2>
                                        <textarea style="width: 100%" cols="60" rows="8" name="fields[eventDetails]"></textarea>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                                @if (!$isLoggedIn)
                                    <div id="step-4">
                                        <div id="form-step-3" role="form" data-toggle="validator">
                                            <div class="form-group">
                                                <h2>Your Name</h2>
                                                <input type="text" class="input-text full-width"
                                                       name="user[name]" required
                                                       value="{{ old('user.name') }}" />
                                                <div class="help-block with-errors"></div>
                                            </div>

                                            <div class="form-group">
                                                <h2>Your Contact Number</h2>
                                                <input type="number" class="input-text full-width"
                                                       name="user[contactNumber]" required
                                                       value="{{ old('user.contactNumber') }}" />
                                                <div class="help-block with-errors"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="step-5" role="form" data-toggle="validator">
                                        <div id="form-step-4" role="form" data-toggle="validator">
                                            <div class="form-group">
                                                <h2>Your Email</h2>
                                                <input type="email" class="input-text full-width"
                                                       name="user[email]" required
                                                       value="{{ old('user.email') }}" />
                                                <div class="help-block with-errors"></div>
                                            </div>
                                            <div class="form-group">
                                                <h2>Create a password Or enter your password if you already registered.</h2>
                                                <input data-minlength="6" type="password" required
                                                       class="input-text full-width"
                                                       name="user[password]" />
                                                <div class="help-block with-errors">Minimum of 6 characters</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{--{!! Form::button('Submit', array('type' => 'submit', 'class' => 'btn-medium' )) !!}--}}
                        {!! Form::close() !!}
                </form>
            </div>
        </div>
    </div>
@endsection

@section('beforebody')
    <style>
        .close-button {
            display: inline-block;
            -webkit-box-sizing: content-box;
            -moz-box-sizing: content-box;
            box-sizing: content-box;
            width: 40px;
            height: 40px;
            position: relative;
            border: none;
            -webkit-border-radius: 1em;
            border-radius: 1em;
            font: normal 8em/normal Arial, Helvetica, sans-serif;
            color: rgba(0,0,0,1);
            -o-text-overflow: clip;
            text-overflow: clip;
            background: #000000;
        }

        .close-button i {
            color: #fff;
            position: absolute;
            font-size: 50px;
            top: -4px;
            line-height: 0;
            left: 9px;
        }

        .provider-request {
            position: relative
        }

        .provider-request .close-button {
            position: absolute;
            top: -15px;
            right: -5px
        }
    </style>
    <script type="text/javascript">
        jQuery('.remove-provider').click(function(e) {
            e.preventDefault();
            var currentId = jQuery(this).data('provider-id');

            var CSRF_TOKEN = jQuery("input[name=_token]").attr('value');
            var data = {id: currentId,_token: CSRF_TOKEN};


            jQuery("#provider-"+currentId).remove();
            jQuery.ajax({
                url: '/request-quote/removeProvider',
                type: 'POST',
                data: data,
                success: function(data) {
                    console.log('removed provider');
                }
            });
        });
    </script>

    <script type="text/javascript" src="/js/validator.js"></script>
    <script type="text/javascript" src="/smartwizard/js/jquery.smartWizard.js"></script>

    <script type="text/javascript">
        jQuery(document).ready(function($){
           $('.sw-btn-group-extra').hide();
            $finshBtn = $('#finish');
            $finshBtn.hide();
            $form = $('#step-form');
            // Step show event
            $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
                //alert("You are on step "+stepNumber+" now");
                if(stepPosition === 'first'){
                    $("#prev-btn").addClass('disabled');
                }else if(stepPosition === 'final'){
                    $("#next-btn").addClass('disabled');
                }else{
                    $("#prev-btn").removeClass('disabled');
                    $("#next-btn").removeClass('disabled');
                }

                if($('button.sw-btn-next').hasClass('disabled')){
                    $('.sw-btn-group-extra').show(); // show the button extra only in the last page
                    $finshBtn.show();
                }else{
                    //$('#finish').hide();
                    $('.sw-btn-group-extra').hide();
                }
            });

            // Toolbar extra buttons
            var btnFinish = $('<a class="button btn-medium green"></a>').text('Finish')
                .addClass('btn btn-info')
                .on('click', function(){
                    var elmForm = $("#form-step-4");

                    // stepDirection === 'forward' :- this condition allows to do the form validation
                    // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
                    if(elmForm){
                        elmForm.validator('validate');
                        var elmErr = elmForm.children('.has-error');
                        if(elmErr && elmErr.length > 0){
                            // Form validation failed
                            return false;
                        }
                    }

                    $form.submit();
                });
            var btnCancel = $('<button></button>').text('Cancel')
                .addClass('btn btn-danger')
                .on('click', function(){ $('#smartwizard').smartWizard("reset"); });


            // Smart Wizard
            var $stepForm = $('#smartwizard');

            $stepForm.smartWizard({
                @if ($errors->has('user.email') OR $errors->has('user.password'))
                    selected: 4,
                @endif
                theme: 'arrows',
                transitionEffect:'fade',
                showStepURLhash: true,
                toolbarSettings: {
                    toolbarExtraButtons: [
                        btnFinish, btnCancel
                    ]
                }
            });

            // External Button Events
            $("#reset-btn").on("click", function() {
                // Reset wizard
                $('#smartwizard').smartWizard("reset");
                return true;
            });

            $("#prev-btn").on("click", function() {
                // Navigate previous
                $('#smartwizard').smartWizard("prev");
                return true;
            });

            $("#next-btn").on("click", function() {
                // Navigate next
                $('#smartwizard').smartWizard("next");
                return true;
            });

            $("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {
                //var elmForm = $(".form.box");
                var elmForm = $("#form-step-" + stepNumber);

                var isSuccess = true;

                // stepDirection === 'forward' :- this condition allows to do the form validation
                // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
                if(stepDirection === 'forward' && elmForm){
                    elmForm.validator('validate');
                    var elmErr = elmForm.children('.has-error');
                    if(elmErr && elmErr.length > 0){
                        // Form validation failed
                        isSuccess = false;
                    }
                }

                var $eventType = $('.search-dropdown').find('select');

                var eventError = checkError($eventType);

                if (eventError === true) {
                    isSuccess = false;
                }

                var $evenDate = $("#eventDate-datepicker");

                var dateError = checkError($evenDate);

                if (dateError === true) {
                    isSuccess = false;
                }

                return isSuccess;
            });

            function checkError($search) {
                var searchVal = $search.val();

                var $formGroup = $search.parents('.form-group');

                var errors = false;

                if (searchVal === "") {
                    $formGroup.addClass('has-error');
                    $formGroup.find('.with-errors').html('<ul class="list-unstyled"><li>Please fill out this field.</li></ul>');
                    errors = true;
                } else {
                    $formGroup.removeClass('has-error');
                    $formGroup.find('.with-errors').html('');
                }

                return errors;
            }
        });
    </script>
@endsection
