@extends('layouts.front')

@push('styles')
    <link href=" {{ asset('front/css/booking-step-2.css') }} " rel="stylesheet">
    <link href=" {{ asset('front/css/booking-step-3.css') }} " rel="stylesheet">
    <style>
        .rupee {
            font-size: 15px;
            font-weight: 500;
        }
        .invalid-feedback {
            display: block !important;
        }
    </style>
@endpush

@section('content')
    <section class="booking_step_section">
        <div class="container">
            @if (!is_null($user))
                {{-- when user is logged in --}}
                <div class="row ">
                    <div class="col-12 booking_step_heading text-center">
                        <h1>@lang('front.summary.checkout.heading.bookingSummary')</h1>
                    </div>
                    <div class="mx-auto step_2_booking_summary">

                        @if ($request_type=='booking')
                            <div class="d-flex justify-content-between">
                                <p>@lang('front.bookingDate')</p>
                                <p>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', $bookingDetails['bookingDate'])->isoFormat('dddd, MMMM Do') }}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>@lang('front.bookingTime')</p>
                                <p>
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $bookingDetails['bookingTime'])->format($settings->time_format) }}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>@lang('front.amountToPay')</p>
                                <p>
                                    {{ currencyFormatter($totalAmount) }}
                                </p>
                            </div>

                            @if(!empty($emp_name))
                                <div class="d-flex justify-content-between">
                                    <p>@lang('app.employee')</p>
                                    <p> {{ $emp_name }} </p>
                                </div>
                            @endif
                        @else
                            <div class="d-flex justify-content-between">
                                <p>@lang('front.amountToPay')</p>
                                <p>
                                    {{ currencyFormatter($totalAmount) }}
                                </p>
                            </div>
                        @endif

                    </div>
                    <div class="mx-auto step_2_booking_summary mt-4">
                        <div class="d-flex justify-content-between">
                            <p>@lang('front.additionalNotes')</p>
                        </div>
                        <form id="booking" method="POST" class="ajax-form">
                            <div class="d-flex justify-content-between">
                                @csrf
                                <input type="hidden" name="request_type" value="{{$request_type}}">
                                <textarea class="form-control" rows="4" name="additional_notes" placeholder="@lang('front.writeYourMessageHere')"></textarea>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row ">
                    <div class="mx-auto step_2_booking_summary mt-4" id="verify-mobile">

                    </div>
                </div>
            @else
                {{-- when user is NOT logged in --}}
                <div class="row ">
                    <div class="col-12 booking_step_heading text-center">
                        <h1>@lang('front.headings.checkout')</h1>
                    </div>
                    <div class="col-12">
                        <div class="checkout_detail d-lg-flex d-block d-md-block">
                            <div class="col-lg-7 col-md-12 booking_step_3_personal_detail">
                                <p class="mb-30">
                                    @lang('front.accountAlready') ?
                                    <a href="{{ route('login') }}"> @lang('front.loginNow') </a>
                                </p>
                                <br>

                                <h2>@lang('app.add') @lang('front.personalDetails')</h2>

                                <form id="personal-details" class="ajax-form" method="POST">
                                    @csrf
                                    <input type="hidden" name="request_type" value="{{$request_type}}">
                                    <input type="hidden" name="customer" value="customer">

                                    <div class="form-row">
                                        <div class="form-group col">
                                            <input type="text" class="step3_input form-control" name="first_name" placeholder="@lang('front.registration.firstName')*" autocomplete="off">
                                        </div>
                                        <div class="form-group col">
                                            <input type="text" class="form-control" name="last_name" placeholder="@lang('front.registration.lastName')*" autocomplete="off">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <input type="text" class="step3_input mt-2 mb-2 form-control" name="vat_number" placeholder="@lang('front.registration.vat_number')" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <input type="email" class="step3_input mt-2 mb-2 form-control" name="email" placeholder="@lang('front.registration.email')*" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group mb-2 mt-2">
                                            <div class="input-group-prepend">
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
                                             <input type="text" class="form-control " name="phone"
                                             placeholder="@lang('front.registration.phoneNumber')"aria-label="Username" aria-describedby="basic-addon1">
                                        </div>
                                    </div>

                                    {{--@if ($googleCaptchaSettings->customer_page == 'active' && $googleCaptchaSettings->status == 'active')
                                        <div class="form-group">
                                            @if ($googleCaptchaSettings->status == 'active')
                                                <input type="hidden" name="recaptcha" class="form-control" id="recaptcha">
                                            @endif
                                            <div id=":captcha_container_div"></div>
                                        </div>
                                    @endif--}}

                                    <div class="d-flex">
                                        <textarea class="form-control" rows="4" name="additional_notes" placeholder="@lang('front.writeYourMessageHere')"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-5 col-md-12  booking_step_3_booking_summary">
                                <h2>@lang('front.summary.checkout.heading.bookingSummary')</h2>

                                @if ($request_type=='booking')
                                    <div class="d-flex justify-content-between mb-4">
                                        <p>@lang('front.bookingDate')</p>
                                        <p>
                                            {{ \Carbon\Carbon::createFromFormat('Y-m-d', $bookingDetails['bookingDate'])->isoFormat('dddd, MMMM Do') }}
                                        </p>
                                    </div>
                                    <div class="d-flex justify-content-between mb-4">
                                        <p>@lang('front.bookingTime')</p>
                                        <p>
                                            {{ \Carbon\Carbon::createFromFormat('H:i:s', $bookingDetails['bookingTime'])->format($settings->time_format) }}
                                        </p>
                                    </div>
                                    <div class="d-flex justify-content-between mb-4">
                                        <p>@lang('front.amountToPay')</p>
                                        <p>
                                            {{ currencyFormatter($totalAmount) }}
                                        </p>
                                    </div>

                                    @if(!empty($emp_name))
                                        <div class="d-flex justify-content-between mb-4">
                                            <p>@lang('app.employee')</p>
                                            <p>{{ $emp_name }} </p>
                                        </div>
                                    @endif

                                @else
                                    <div class="d-flex justify-content-between mb-4">
                                        <p>@lang('front.amountToPay')</p>
                                        <p>
                                            {{ currencyFormatter($totalAmount) }}
                                        </p>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

            @endif


            {{-- NAVIGATION --}}
            <div class="row">
                <div class="col-12 d-flex justify-content-between booking_step_buttons">

                    <button class="btn d-flex align-items-center go-back">
                        <i class="zmdi zmdi-long-arrow-left"></i>
                        @lang('front.navigation.goBack')
                    </button>

                    @if (!$user)
                        @if ($smsSettings->nexmo_status == 'active')
                        <button class="btn d-flex align-items-center save-user">
                            @lang('front.navigation.toVerifyMobile')
                            <i class="zmdi zmdi-long-arrow-right"></i>
                        </button>
                        @else
                            <button class="btn d-flex align-items-center save-user">
                                @lang('front.navigation.toPayment')
                                <i class="zmdi zmdi-long-arrow-right"></i>
                            </button>
                        @endif
                    @else
                        @if ($smsSettings->nexmo_status == 'active')
                            <button class="btn d-flex align-items-center save-booking"  @if (!$user->mobile_verified) disabled @endif>
                                @lang('front.navigation.toPayment')
                                <i class="zmdi zmdi-long-arrow-right"></i>
                            </button>
                        @else
                            <button class="btn d-flex align-items-center save-booking">
                                @lang('front.navigation.toPayment')
                                <i class="zmdi zmdi-long-arrow-right"></i>
                            </button>
                        @endif
                    @endif

                </div>
            </div>
            {{-- NAVIGATION --}}


        </div>
    </section>
@endsection

@push('footer-script')
    {{--@if ($googleCaptchaSettings->v2_status == 'active' && $googleCaptchaSettings->status == 'active')
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
    async defer></script>
        <script>
            var gcv3;
            var onloadCallback = function() {
                if ( $('#captcha_container_div').length ) {
                    gcv3 = grecaptcha.render('captcha_container_div', {
                        'sitekey' : '{{$googleCaptchaSettings->v2_site_key}}',
                        'theme' : 'light',
                        'callback' : function(response) {
                            console.log(response);
                            if(response) {

                                document.getElementById('recaptcha').value = response;
                            }
                        },
                    });
                }
            };


            /*var onloadCallback = function() {
                    // Renders the HTML element with id ':captcha_container_div' as a reCAPTCHA widget.
                    // The id of the reCAPTCHA widget is assigned to 'gcv3'.
                    gcv3 = grecaptcha.render('captcha_container_div', {
                    'sitekey' : '{{$googleCaptchaSettings->v2_site_key}}',
                    'theme' : 'light',
                    'callback' : function(response) {
                        if(response) {

                            document.getElementById('recaptcha').value = response;
                        }
                    },
                });
            };*/
        </script>
    @endif--}}

    {{--@if ($googleCaptchaSettings->v3_status == 'active' && $googleCaptchaSettings->status == 'active')
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
    @endif--}}

    <script>
        $('body').on('click', '.save-user', function () {
            var location = localStorage.getItem('location');
            var url =  '{{ route('front.saveBooking') }}';
            var form = $('#personal-details');

            $.easyAjax({
                url: url,
                type: 'POST',
                data: form.serialize()+'&location='+location,
                blockUI: false,
                disableButton: true,
                buttonSelector: ".save-user",
                success: function (response){
                    console.log(response);
                },
                error: function (error){
                    console.log(error);
                }
            })
        })

        $('body').on('click', '.save-booking', function () {
            var location = localStorage.getItem('location');

            $.easyAjax({
                url: '{{ route('front.saveBooking') }}',
                type: 'POST',
                data: $('#booking').serialize()+'&location='+location,
                blockUI: false,
                disableButton: true,
                buttonSelector: ".save-booking",
                error: function (error){
                    console.log(error);

                    if( error.status === 422 ) {
                        var data = error.responseJSON.errors
                    }
                    $.each( data, function( key, value ) {
                        $.showToastr(value[0], 'error');
                    });
                }
            })
        })

        @if ($smsSettings->nexmo_status == 'active' && $user && !$user->mobile_verified && !session()->has('verify:request_id'))
            sendOTPRequest();
        @endif

        var x = '';

        function clearLocalStorage() {
            localStorage.removeItem('otp_expiry');
            localStorage.removeItem('otp_attempts');
        }

        function checkSessionAndRemove() {
            $.easyAjax({
                url: '{{ route('removeSession') }}',
                type: 'GET',
                data: {'sessions': ['verify:request_id']}
            })
        }

        function startCounter(deadline) {
            x = setInterval(function() {
                var now = new Date().getTime();
                var t = deadline - now;

                var days = Math.floor(t / (1000 * 60 * 60 * 24));
                var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60));
                var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((t % (1000 * 60)) / 1000);

                $('#demo').html('Time Left: '+minutes + ":" + seconds);
                $('.attempts_left').html(`${localStorage.getItem('otp_attempts')} attempts left`);

                if (t <= 0) {
                    clearInterval(x);
                    clearLocalStorage();
                    checkSessionAndRemove();
                    location.href = '{{ route('front.cartPage') }}'
                }
            }, 1000);
        }

        if (localStorage.getItem('otp_expiry') !== null) {
            let localExpiryTime = localStorage.getItem('otp_expiry');
            let now = new Date().getTime();

            if (localExpiryTime - now < 0) {
                clearLocalStorage();
                checkSessionAndRemove();
            }
            else {
                $('#otp').focus().select();
                startCounter(localStorage.getItem('otp_expiry'));
            }
        }

        function sendOTPRequest() {
            $.easyAjax({

                url: '{{ route('sendOtpCode') }}',
                type: 'POST',
                container: '#request-otp-form',
                messagePosition: 'inline',
                data: {
                    data: $('#request-otp-form').serialize(),
                    _token: '{{csrf_token()}}'
                },
                success: function (response) {
                    console.log(response);
                    if (response.status == 'success') {
                        localStorage.setItem('otp_attempts', 3);

                        $('#verify-mobile').html(response.view);
                        $('.attempts_left').html(`3 attempts left`);

                        $('#otp').focus();

                        var now = new Date().getTime();
                        var deadline = new Date(now + parseInt('{{ config('nexmo.settings.pin_expiry') }}')*1000).getTime();

                        localStorage.setItem('otp_expiry', deadline);
                        startCounter(deadline);
                        // intialize countdown
                    }
                    if (response.status == 'fail') {
                        $('#mobile').focus();
                    }
                },
                error: function (error){
                    console.log(error);
                }
            });
        }

        function sendVerifyRequest() {
            $.easyAjax({
                url: '{{ route('verifyOtpCode') }}',
                type: 'POST',
                container: '#verify-otp-form',
                messagePosition: 'inline',
                data: $('#verify-otp-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        clearLocalStorage();

                        location.reload();
                        $('#verify-mobile').html('');

                        // select2 reinitialize
                        $('.select2').select2();
                    }
                    if (response.status == 'fail') {
                        // show number of attempts left
                        let currentAttempts = localStorage.getItem('otp_attempts');

                        if (currentAttempts == 1) {
                            clearLocalStorage();
                        }
                        else {
                            currentAttempts -= 1;

                            $('.attempts_left').html(`${currentAttempts} attempts left`);
                            $('#otp').focus().select();
                            localStorage.setItem('otp_attempts', currentAttempts);
                        }

                        if (Object.keys(response.data).length > 0) {
                            $('#verify-mobile').html(response.data.view);

                            // select2 reinitialize
                            $('.select2').select2();

                            clearInterval(x);
                        }
                    }
                }
            });
        }

        $('body').on('submit', '#request-otp-form', function (e) {
            e.preventDefault();
            sendOTPRequest();
        });

        $('body').on('click', '#request-otp', function () {
            sendOTPRequest();
        });

        $('body').on('submit', '#verify-otp-form', function (e) {
            e.preventDefault();
            sendVerifyRequest();
        });

        $('body').on('click', '#verify-otp', function() {
            sendVerifyRequest();
        });

        $('body').on('click', '.go-back', function() {
            var url = "{{ url()->previous() }} ";
            window.location.href = url;
        });


    </script>
@endpush
