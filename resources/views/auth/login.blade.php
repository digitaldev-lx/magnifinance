@extends('layouts.front')

@push('styles')
    <link href="front/css/login-register.css" rel="stylesheet">
    <style>
    .invalid-feedback {
        display: block !important;
    }
    
    </style>
@endpush

@section('content')
    <!-- BOOKING SECTION START -->
    <section class="booking_step_section">
        <div class="container">
            <div class="row">
                <div class="col-12 booking_step_heading text-center">
                    <h1>@lang('app.welcomeTo') <span>{{$frontThemeSettings->title }}</span> !</h1>
                </div>
                <div class="form_wrapper mx-auto position-relative">
                    <form action="{{ route('login') }}" method="post">
                        @csrf
                        
                        <span class="form_icon"><i class="zmdi zmdi-key"></i></span>

                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required autofocus placeholder="@lang('app.email')*" id="username" aria-describedby="username">
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mt-4">
                            <input type="password" id="password" class="form-control form-control-lg {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="@lang('app.password')*" id="Password" aria-describedby="Password">
                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="remember_box mt-3 d-flex justify-content-between">
                            <input name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} type="checkbox">
                            <label for="remember" class="mb-3">
                                <span></span>@lang('app.rememberMe')
                            </label>
                            <a href="{{ route('password.request') }}">@lang('app.forgotPassword')</a>
                        </div>

                        @if ($googleCaptchaSettings->login_page == 'active' && $googleCaptchaSettings->status == 'active')
                            <div class="form-group">
                                <div class="centering v-center mb-2 mt-2">
                                    <input type="hidden" name="recaptcha" class="form-control" id="recaptcha" value="">   
                                    <div id="captcha_container"></div>
                                </div>
                            </div>
                        @endif
                        @if ($errors->has('recaptcha'))
                            <span class="invalid-feedback text-left" role="alert">
                                <strong>{{ $errors->first('recaptcha') }}</strong>
                            </span>
                        @endif

                        <button type="submit" class="btn btn-dark mx-auto d-block mt-4">@lang('app.signIn')</button>
                    </form>

                    <!-- /.social-auth-links -->
                    @if($socialAuthSettings->google_status == 'active' || $socialAuthSettings->facebook_status == 'active')
                        <div class="social-auth-links text-center mb-3">
                            <p class="mt-3 mb-3">- @lang('front.or') -</p>
                            <div class="col-md-12 p-0">
                                <div class="d-flex justify-content-between">
                                    @if($socialAuthSettings->google_status == 'active')
                                        <a class="btn btn-outline-dark px-2" href="{{ route('social.login', 'google') }}" role="button" style="text-transform:none">
                                            <img width="20px" style="margin-bottom:3px; margin-right:5px" alt="Google sign-in" src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png" />
                                            </i>@lang('app.signIn') @lang('app.using') @lang('app.google')
                                        </a>
                                    @endif
                                    @if($socialAuthSettings->facebook_status == 'active')
                                        <a class="btn btn-outline-dark px-2" href="{{ route('social.login', 'facebook') }}" role="button" style="text-transform:none;">
                                            <i class="fab fa-facebook mr-2"></i>@lang('app.signIn') @lang('app.using') @lang('app.facebook')
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    <!-- /.social-auth-links -->
                </div>
            </div>
        </div>
    </section>
    <!-- BOOKING SECTION END -->
@endsection

@if ($googleCaptchaSettings->login_page == 'active' && $googleCaptchaSettings->v2_status == 'active' && $googleCaptchaSettings->status == 'active')
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
    async defer></script>
    <script>
        var gcv3;
        var onloadCallback = function() {
                // Renders the HTML element with id 'captcha_container' as a reCAPTCHA widget.
                // The id of the reCAPTCHA widget is assigned to 'gcv3'.
                gcv3 = grecaptcha.render('captcha_container', {
                'sitekey' : '{{$googleCaptchaSettings->v2_site_key}}',
                'theme' : 'light',
                'callback' : function(response) {
                    if(response) {
                        document.getElementById('recaptcha').value = response;
                    }
                },
            });
        };
    </script>
@endif

@if ($googleCaptchaSettings->login_page == 'active' && $googleCaptchaSettings->v3_status == 'active' && $googleCaptchaSettings->status == 'active')
    <script src="https://www.google.com/recaptcha/api.js?render={{ $googleCaptchaSettings->v3_site_key }}"></script>
    <script>
        grecaptcha.ready(function() {
            grecaptcha.execute('{{ $googleCaptchaSettings->v3_site_key }}', {action: 'contact'}).then(function(token) {
                if (token) {
                    document.getElementById('recaptcha').value = token;
                }
            });
        });
    </script>
@endif
