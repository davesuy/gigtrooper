@extends('layouts.app')

@section('content')
    <div id="main">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class=" image-style style1 large-block">
                    <h2>{{ $page->title }}</h2>
                    {!! $page->excerpt !!}
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">Register</div>
                    <div class="panel-body">
                        @if (session('status'))
                            <div class="booking-information travelo-box">
                                <div class="booking-confirmation clearfix">
                                    <i class="soap-icon-recommend icon circle"></i>
                                    <div class="message">
                                        <h4 class="main-message">Confirm Your Email Address.</h4>
                                        <p>We have sent an email with a confirmation link to your
                                            email
                                            address "{{ old('email') }}".<br/>
                                            Please allow 5-10 minutes for this message to arrive.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <form class="form-horizontal" role="form" method="POST"
                                  action="{{ route('register') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <label for="name" class="col-md-4 control-label">Name</label>

                                    <div class="col-md-6">
                                        <input id="name" type="text" class="form-control"
                                                name="name" value="{{ old('name') }}" required
                                               autofocus>

                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email" class="col-md-4 control-label">E-Mail
                                        Address</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control"
                                               name="email" value="{{ old('email') }}" required>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('register.countryCode') ? ' has-error' : '' }}">
                                    <label for="email"
                                           class="col-md-4 control-label">Country</label>

                                    <div class="col-md-6">
                                        {!! \App::make('countryService')->getCountriesDropDown('PH', 'register') !!}
                                        @if ($errors->has('register.countryCode'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('register.countryCode') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('register.category') ? ' has-error' : '' }}">
                                    <label for="email"
                                           class="col-md-4 control-label">Category</label>

                                    <div class="col-md-6">
                                        {!! \TemplateHelper::getMemberCategoryDropdown() !!}
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password"
                                           class="col-md-4 control-label">Password</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control"
                                               name="password" required>

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password-confirm" class="col-md-4 control-label">Confirm
                                        Password</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password"
                                               class="form-control" name="password_confirmation"
                                               required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary">
                                            Register
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div class="form-group text-center"><strong>OR</strong>
                                <a
                                        href="/login/facebook{{ ($idValue = \Request::get('categoryId'))? '?categoryId=' .
                              $idValue : '' }}">
                                    <img src="/images/fblogin.png" alt="login with facebook"/></a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('header')
    <link href="{{ mix('css/ui-dropdown.css') }}" rel="stylesheet"/>
    <!-- Facebook Pixel Code -->
    <script>
        !function(f, b, e, v, n, t, s) {
            if (f.fbq) {
                return;
            }
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) {
                f._fbq = n;
            }
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '296160980890765');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=296160980890765&ev=PageView&noscript=1"
        /></noscript>
    <!-- End Facebook Pixel Code -->

@endsection

@section('beforebody')
    <script type="text/javascript" src="{{ mix('js/ui-dropdown.js') }}"></script>
    <script type="text/javascript" src="/js/jquery.ui.touch-punch.min.js"></script>
@endsection