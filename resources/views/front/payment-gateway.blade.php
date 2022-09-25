@extends('layouts.front')

@push('styles')
    <link href=" {{ asset('front/css/booking-step-4.css') }} " rel="stylesheet">
    <style>
        .rupee {
            font-size: 18px;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
    <!-- PAYMENT GATEWAY START -->
        <section class="booking_step_section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-12">
                        <div class="booking_step_4_booking_summary">
                            <h2 class="text-center">@lang('front.summary.checkout.heading.bookingSummary')</h2>

                            @if ($booking->deal_id!='')
                                <div class="d-flex justify-content-between mb-4">
                                    <p>@lang('app.deal') @lang('app.name')</p>
                                    <p>{{$booking->deal->title}}</p>
                                </div>
                                <div class="d-flex justify-content-between mb-4">
                                    <p>@lang('front.amount')</p>
                                    <p> {{ $booking->formated_amount_to_pay }}</p>
                                </div>
                                <div class="d-flex justify-content-between mb-4">
                                    <p>@lang('front.offeredBy')</p>
                                    <p>{{$booking->company->company_name}}</p>
                                </div>
                            @else
                                <div class="d-flex justify-content-between mb-4">
                                    <p>@lang('front.bookingDate')</p>
                                    <p>{{ $booking->date_time->isoFormat('dddd, MMMM Do') }}</p>
                                </div>

                                <div class="d-flex justify-content-between mb-4">
                                    <p>@lang('front.bookingTime')</p>
                                    <p>{{ $booking->date_time->format($settings->time_format) }}</p>
                                </div>

                                <div class="d-flex justify-content-between mb-4">
                                    <p>@lang('front.amountToPay')</p>
                                    <p> {{ $booking->formated_amount_to_pay }}</p>
                                </div>

                                @if(!empty($emp_name))
                                    <div class="d-flex justify-content-between mb-4">
                                        <p> @lang('app.employee')</p>
                                        <p> {{ $emp_name }}</p>
                                    </div>
                                @endif

                            @endif
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-12">
                        <div class="payment_options mobile-no-padding pl-md-0 pt-lg-0 pt-5">
                            <h2 class="mobile-no-padding">@lang('front.paymentMethod')</h2>
                            <div class="row">
                                @if ($credentials->stripe_status === 'active')
                                    <div class="col-md-6 col-12 mb-3 pb-lg-0 pb-md-0">
                                        <a class="payment_icon_name" href="javascript:;" id="stripePaymentButton">
                                            <div class="payment_icon_box">
                                                <span>
                                                    <i class="fa fa-cc-stripe"></i>
                                                </span>
                                            </div>
                                            <span class="payment_name">{{__('modules.invoices.payNow')}}</span>
                                        </a>
                                    </div>
                                @endif

                                @if ($credentials->razorpay_status === 'active')
                                <div class="col-md-6 col-12 mb-3 pb-lg-0 pb-md-0">
                                    <a id="razorpayButton" class="payment_icon_name" href="#">
                                        <div class="payment_icon_box">
                                            <span>
                                                <i class="fa fa-registered"></i>
                                            </span>
                                        </div>
                                        <span class="payment_name">@lang('front.buttons.razorpay')</span>
                                    </a>
                                </div>
                                @endif
                                @if ($credentials->offline_payment)
                                    <div class="col-md-6 col-12 mb-3 pb-lg-0 pb-md-0">
                                        <a href="{{ route('front.offline-payment') }}" class="payment_icon_name" id="offline_payment">
                                            <div class="payment_icon_box">
                                                <span>
                                                    <i class="fa fa-money"></i>
                                                </span>
                                            </div>
                                            <span class="payment_name">@lang('front.buttons.offlinePayment')</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 d-flex justify-content-around booking_step_buttons">
                        <button class="d-flex align-items-center navigation-to-account">
                            <i class="zmdi zmdi-home"></i> @lang('front.navigation.toAccount')
                        </button>
                    </div>
                </div>
            </div>
        </section>
    <!-- PAYMENT GATEWAY END -->
@endsection

@push('footer-script')

    @if($credentials->razorpay_status == 'active')
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            // put customer in razorpay payment flow
            var options = {
                key: "{{ $credentials->razorpay_key }}", // Enter the Key ID generated from the Dashboard
                amount: "{{ $booking->converted_amount_to_pay * 100 }}", // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
                currency: "{{ $globalSetting->currency->currency_code }}",
                name: "{{ $booking->user->name }}",
                description: "@lang('app.booking') @lang('front.headings.payment')",
                image: "{{ $setting->logo_url }}",
                order_id: "{{ $booking->order_id }}", //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
                "handler": function (response){
                    $.easyAjax({
                        url: "{{ route('front.razorpay.verifyPayment') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            order_id: '{{ $booking->order_id }}',
                            razorpay_signature: response.razorpay_signature,
                        },
                        container: '#invoice_container',
                        redirect: true,

                    })
                },
                prefill: {
                    name: "{{ $booking->user->name }}",
                    email: "{{ $booking->user->email }}",
                    contact: "{{ $booking->user->mobile }}"
                },
                notes: {
                    booking_id: "{{ $booking->id }}"
                },
                theme: {
                    color: "{{ $frontThemeSettings->primary_color }}"
                }
            };
            var rzp1 = new Razorpay(options);
            document.getElementById('razorpayButton').onclick = function(e){
                rzp1.open();
                e.preventDefault();
            }
        </script>
    @endif

    @if($credentials->stripe_status == 'active')
        <script src="https://js.stripe.com/v3/"></script>
        <script>

            var stripe = Stripe('{{ $credentials->stripe_client_id }}');
            var checkoutButton = document.getElementById('stripePaymentButton');

            checkoutButton.addEventListener('click', function() {
                $.easyAjax({
                    url: '{{route('front.stripe')}}',
                    container: '#invoice_container',
                    type: "POST",
                    redirect: true,
                    async: false,
                    data: {"_token" : "{{ csrf_token() }}", 'booking_id' : {{$booking->id }}, 'return_url' : 'frontend' },
                    beforeSend: function ( xhr ) {
                        jQuery("#page-loader").removeClass("d-none");
                        $("#page-loader").show();
                        $(".loader").show();
                    },
                    success: function(response){
                        console.log(response)
                        jQuery("#page-loader").addClass("d-none");

                        stripe.redirectToCheckout({
                            sessionId: response.id,
                        }).then(function (result) {
                            if (result.error) {
                                $.easyAjax({
                                    url: '{{route('front.redirectToErrorPage')}}',
                                });
                            }
                        });
                    },
                    error: function(response){
                        console.log(response)

                    },
                    complete : $(".loader").hide()
                });
            });
        </script>
    @endif

    <script>
        $('body').on('click', '#offline_payment', function() {
            $('#page-load-msg').html('Processing Payment... <b>Do Not Refresh the Page</b>.');
            document.getElementById("page-loader").style.display = "flex";
            location.href = '{{ route('front.offline-payment') }}'
        });

        $('body').on('click', '#offline_payment, #stripePaymentButton, #razorpayButton', function() {
            if ($('a').hasClass('disabled')) {
                $('a').removeClass('disabled');
            } else {
                $('a').addClass('disabled');
            }
        });

        $('body').on('click', '.navigation-to-account', function() {
            var url = "{{ route('admin.dashboard') }} ";
            window.location.href = url;
        });
    </script>
@endpush
