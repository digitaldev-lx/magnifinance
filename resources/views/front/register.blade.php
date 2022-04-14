@extends('layouts.front')

@push('styles')
    <link href="{{ asset('front/css/login-register.css') }}" rel="stylesheet">
    <style>
        @media (max-width: 767px){
            #password-strength{
                font-size: 11px;
            }
            .w-sm-100{
                width: 100%;
            }
        }

        .required-spa n{
            color: red;
        }

        .parsley-errors-list {
            list-style: none;
            display: block;
            width: 100%;
            padding: 0px;
        }

        ul.parsley-errors-list li {
            left: 0px;
        }

        .parsley-required, .parsley-type, .parsley-length, .parsley-minlength, .parsley-pattern, .parsley-min, .parsley-range {
            color: red;
        }
        /* #prepend {
            max-width: 35%;
            min-width: 34%;
        } */
        .select2-container--default .select2-selection--single .select2-selection__rendered:focus, .select2-container--default .select2-selection--single:focus {
            outline: 0;
        }
        #parsley-id-9 {
            order: 1;
        }
        #basic-addon1 {
            background-color: #fff;
        }
        .d-none {
           display: none;
        }
        .registrationSuccess {
            margin-top:10%;
            margin-bottom:10%;
        }
        #password-strength-div {
            background-color: transparent;
        }
        .password-msg-span {
            background-color: transparent;
            border-left: 0px;
        }
        #password-label {
            width: 100%;
        }
        .logo-image img {
            max-width: 100%;
            width: unset;
            object-fit: contain;
        }
    </style>
@endpush

@section('content')
    <!-- REGISTRATION SECTION START -->
    <section class="booking_step_section">
        <div class="container">
            <div class="row registrationDiv">
                <div class="col-lg-8 col-11 form_wrapper mx-auto position-relative">
                    <form method="POST" id="registration-form" role="form" data-parsley-validate="">
                        @csrf
                        <span class="form_icon"><i class="zmdi zmdi-globe-alt"></i></span>

                        <div class="form-group">
                            <label for="name">@lang('app.companyBusinessIndividualName') <span class="required-span">*</span> </label>
                            <input data-parsley-trigger="keyup" autocomplete="off" required type="text" name="business_name" id="business_name" class="form-control form-control-lg {{ $errors->has('business_name') ? ' is-invalid' : '' }}" value="{{ old('business_name') }}" required autofocus placeholder="@lang('app.placeholder.CompanyBusinessIndividualName')" data-parsley-minlength="3">
                            @if ($errors->has('business_name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('business_name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="vat_number">{{__("app.companyVatNumber")}}<span class="required-span">*</span> </label>
                            <input data-parsley-trigger="keyup" autocomplete="off" required type="text" name="vat_number" id="vat_number" class="form-control form-control-lg {{ $errors->has('vat_number') ? ' is-invalid' : '' }}" value="{{ old('vat_number') }}" required autofocus placeholder="{{__('app.placeholder.vat_number')}}" data-parsley-minlength="3">
                            @if ($errors->has('vat_number'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('vat_number') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="email">@lang('app.businessEmail') <span class="required-span">*</span> </label>
                            <input data-parsley-trigger="keyup" autocomplete="off"  required type="email" name="email" id="email" class="form-control form-control-lg {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required autofocus placeholder="@lang('app.placeholder.BusinessEmail')" id="username" aria-describedby="username">
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="input-group">
                            <label id="password-label" for="password">@lang('app.password') <span class="required-span">*</span> </label>
                            <input data-parsley-trigger="keyup" onKeyUp="checkPasswordStrength();" autocomplete="off"  required type="password" name="password" id="password" class="form-control border-right-lg-0 form-control-lg {{ $errors->has('password') ? ' is-invalid' : '' }}" value="{{ old('password') }}" required id="password">
                            <div class="input-group-append order-0 w-sm-100">
                                <span class="input-group-text w-sm-100" id="password-strength-div">
                                    <span id="password-strength" class="text-danger">@lang('app.placeholder.Password.Weak')
                                    </span>
                                    &nbsp;&nbsp;
                                    <span class="fa fa-info-circle text-default password-msg-span" data-toggle="tooltip" data-placement="top" title="Password Should contains atleast 1 alphabets, 1 numbers and 1 special characters"></span>
                                </span>

                            </div>
                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group input-group mb-2 mt-2">
                            <label for="calling_code" class="btn-block">@lang('app.businessContact') <span class="required-span">*</span> </label>
                            <div class="input-group-prepend business-contact" id="prepend">
                                <span class="input-group-text" id="basic-addon1">
                                <select name="calling_code" id="calling_code" class="form-control myselect">
                                    @foreach ($calling_codes as $code => $value)
                                        <option value="{{ $value['dial_code'] }}"
                                        @if (!is_null($user) && $user->calling_code)
                                            {{ $user->calling_code == $value['dial_code'] ? 'selected' : '' }}
                                        @endif>{{ $value['dial_code'] . ' - ' . $value['name'] }}</option>
                                    @endforeach
                                </select>
                                </span>
                            </div>
                                <input data-parsley-trigger="keyup" autocomplete="off"  required id="contact" type="number" class="form-control " name="contact" data-parsley-type="integer"
                                placeholder="@lang('app.placeholder.ContactNumber') {{ $errors->has('contact') ? ' is-invalid' : '' }}" value="{{ old('contact') }}">
                        </div>

                        <div class="form-group">
                            <label for="website">@lang('app.businessWebsite') </label>
                            <input autocomplete="off"  type="url" name="website" id="website" class="form-control form-control-lg {{ $errors->has('website') ? ' is-invalid' : '' }}" value="{{ old('website') }}" placeholder="www.example.com" data-parsley-type="url">
                            @if ($errors->has('website'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('website') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mt-4">
                            <label for="name">@lang('app.yourName')<span class="required-span">*</span> </label>
                            <input data-parsley-trigger="keyup" autocomplete="off"  required type="text" id="name" name="name" class="form-control form-control-lg {{ $errors->has('name') ? ' is-invalid' : '' }}"  required placeholder="John Doe" data-parsley-minlength="3" value="{{ old('name') }}">
                            @if ($errors->has('name'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mt-4">
                            <label for="address">@lang('app.address')<span class="required-span">*</span> </label>
                            <textarea data-parsley-trigger="keyup" required class="form-control {{ $errors->has('address') ? ' is-invalid' : '' }}" name="address" id="address" rows="3" placeholder="Eg. Near Statue of Liberty National Monument, New York City, United States." data-parsley-minlength="10"> {{ old('address') }} </textarea>
                            @if ($errors->has('address'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('address') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mt-4">
                            <label for="post_code">{{__('app.businessPostCode')}}<span class="required-span">*</span> </label>
                            <input data-parsley-trigger="keyup" autocomplete="off"  required type="text" id="post_code" name="post_code" class="form-control form-control-lg {{ $errors->has('post_code') ? ' is-invalid' : '' }}"  required placeholder="{{__('app.placeholder.post_code')}}" data-parsley-minlength="3" value="{{ old('post_code') }}">
                            @if ($errors->has('post_code'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('post_code') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group input-group mb-2 mt-2">
                            <label for="city" class="btn-block">{{__('app.businessCityCountry')}} <span class="required-span">*</span> </label>
                            <input data-parsley-trigger="keyup" autocomplete="off"  required id="city" type="text" class="form-control " name="city" data-parsley-minlength="3"
                                   placeholder="{{__('app.placeholder.city')}} {{ $errors->has('city') ? ' is-invalid' : '' }}" value="{{ old('city') }}">
                            <div class="input-group-append business-contact" id="prepend">
                                <span class="input-group-text" id="basic-addon1">
                                <select name="country_id" id="country_id" class="form-control myselect">
                                    @foreach ($countries as $country => $value)
                                        <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                                    @endforeach
                                </select>
                                </span>
                            </div>
                         </div>

                        @if ($googleCaptchaSettings->vendor_page == 'active' && $googleCaptchaSettings->status == 'active')
                            <div class="form-group">
                                <input type="hidden" name="recaptcha" class="form-control" data-parsley-trigger="keyup" required id="recaptcha">
                                <div id="captcha_container"></div>
                            </div>
                        @endif
                        @if ($errors->has('recaptcha'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('recaptcha') }}</strong>
                            </span>
                        @endif

                        <div class="form-group remember_box mt-3 d-block d-lg-flex d-md-flex justify-content-between">
                            <input autocomplete="off"  id="read_agreement" type="checkbox">
                            <label for="read_agreement" class="mb-3">
                                <span></span>{{ $settings->terms_note }}&nbsp;

                            </label>
                        </div>

                        <button disabled id="save-form" type="submit" class="btn btn-dark mx-auto d-block mt-4">@lang('app.createMyAccount')</button>

                    </form>
                </div>
            </div>
            <div class="row successfulRegDiv d-none">
                <div class="container registrationSuccess">
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="all-title">
                                <h3 class="sec-title">@lang('app.registrationSuccess')</h3>
                            </div>
                        </div>
                    </div><br>
                    <div class="billing-info payment-box success-box">
                        <div class="alert alert-success text-center" role="alert">
                            <strong>@lang('app.success')!</strong> {{ $settings->sign_up_note }}
                        </div>
                    </div><br>
                    <div class="row mt-30">
                        <div class="col-12 text-center">
                            <a href="{{ route('front.index') }}" class="btn btn-custom">
                                <i class="fa fa-home mr-2"></i>
                                @lang('app.backToHome')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- REGISTRATION SECTION END -->
@endsection

@push('footer-script')

    @if ($googleCaptchaSettings->v2_status == 'active' && $googleCaptchaSettings->status == 'active')
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

    @if ($googleCaptchaSettings->v3_status == 'active' && $googleCaptchaSettings->status == 'active')
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

    <script src="{{ asset('js/parsley.min.js') }}"></script>

    <script>
        $(document).ready(function () {

            $('#registration-form').parsley();

            $('[data-toggle="tooltip"]').tooltip();

            $("body").on('click', '#read_agreement', function () {
                $("#save-form").attr("disabled", !this.checked);
            });
        });

        $('#registration-form').on('submit', function(event) {
            event.preventDefault();
            if($('#registration-form').parsley().isValid())
            {
                $.easyAjax({
                    url: '{{ route("front.storeCompany") }}',
                    type: 'POST',
                    container: '#registration-form',
                    data: $('#registration-form').serialize(),
                    redirect: true,
                    beforeSend:function()
                    {
                        $('#save-form').attr('disabled', 'disabled');
                        $('#save-form').html('Submitting...');
                    },
                    success: function (response) {
                        console.log(response)
                        if(response.status == 'success') {
                            $('.registrationDiv').hide();
                            $('.successfulRegDiv').removeClass('d-none');
                            $('#save-form').html('Create My Account');
                            $('#registration-form')[0].reset();
                            $('#registration-form').parsley().reset();

                            $('html, body').animate({
                                scrollTop: $(".booking_step_section").offset().top
                            }, 1000);

                        }
                        else {
                            $('#save-form').removeAttr('disabled');
                            $('#save-form').html("{{__('app.createMyAccount')}}");
                        }
                    },
                    error: function (error) {
                        console.log(error);
                        if (error.status === 422) {
                            var data = error.responseJSON.errors
                        }
                        $.each(data, function (key, value) {
                            $.showToastr(value[0], 'error');
                        });
                        $("#save-form").prop("disabled", false);
                        $('#save-form').html("{{__('app.createMyAccount')}}");
                    }
                });
            }
        });

        function checkPasswordStrength() {
            var number = /([0-9])/;
            var alphabets = /([a-zA-Z])/;
            var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;
            if ($('#password').val().length < 6) {
                $('#password-strength').removeClass('text-success text-warning');
                $('#password-strength').addClass('text-danger');
                $('#password-strength').html("@lang('app.placeholder.Password.Weak')");
            }
            else
            {
                if ($('#password').val().match(number) && $('#password').val().match(alphabets) && $('#password').val().match(special_characters))  {
                    $('#password-strength').removeClass('text-danger text-warning');
                    $('#password-strength').addClass('text-success');
                    $('#password-strength').html("@lang('app.placeholder.Password.Strong')");
                }
                else  {
                    $('#password-strength').removeClass('text-success text-danger');
                    $('#password-strength').addClass('text-warning');
                    $('#password-strength').html("@lang('app.placeholder.Password.Medium')");
                }
            }
        }
    </script>
@endpush

