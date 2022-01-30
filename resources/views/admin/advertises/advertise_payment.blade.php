<style>
    <link href="{{ asset('front/css/bootstrap-datepicker.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/plugins/iCheck/all.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Lato&family=Poppins:wght@400;500&family=Quicksand:wght@700&display=swap" rel="stylesheet">
    <link href=" {{ asset('front/css/booking-step-4.css') }} " rel="stylesheet">

    .call-to-action {
        width: 100%;
    }

    .call-to-action a {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 1.3em;
    }

    .priceContainer {
        margin-top: 1rem;
    }

    .price {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 1.7em;
    }

    .description {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 1.3em;
    }

    .deal_detail_img {
        border-radius: 7px;
        overflow: hidden;
    }

    .deal_detail_img img {
        transition: all 0.2s linear;
        object-fit: cover;
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16);
    }

    .deal_detail_img:hover img {
        -webkit-transform: scale(1.1, 1.1);
        -moz-transform: scale(1.1, 1.1);
        -o-transform: scale(1.1, 1.1);
        -ms-transform: scale(1.1, 1.1);
        transform: scale(1.1, 1.1);
        transition: all 0.2s linear;
    }

    .deal_detail_box h3 {
        font-size: 20px;
        font-weight: 500;
        font-stretch: normal;
        font-style: normal;
        letter-spacing: normal;
        color: var(--primary-color);
        margin-top: 7px;
        margin-bottom: 17px;
    }

    .deal_detail_box h2 {
        font-family: 'Poppins', sans-serif !important;
        font-size: 28px;
        font-weight: 500;
        font-stretch: normal;
        font-style: normal;
        line-height: 1.57;
        letter-spacing: normal;
        color: #232323;
        margin-bottom: 26px;
    }

    .deal-detail_box p {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
        font-size: 16px;
    }

    .deal_detail_contact {
        font-size: 12px;
        color: #989898;
        border-bottom: 1px solid #989898;
        padding-bottom: 32.5px;
    }

    .deal_detail_contact span, .deal_detail_contact a {
        font-size: 16px;
        font-weight: normal;
        font-stretch: normal;
        font-style: normal;
        letter-spacing: normal;
        color: #2d2d2d;
    }


    .collapse.in {
        display: block;
    }

    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #999;
    }

    .select2-dropdown .select2-search__field:focus, .select2-search--inline .select2-search__field:focus {
        border: 0px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        margin: 0 13px;
    }

    .select2-container--default .select2-selection--multiple {
        border: 1px solid #cfd1da;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        cursor: pointer;
        float: right;
        font-weight: bold;
        margin-top: 8px;
        margin-right: 15px;
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }

    .select2 {
        width: 100%;
    }

    textarea {
        min-height: 173px;
    }

    #d-none {
        display: none;
    }

    #day-div {
        margin-left: 1em
    }

    #icheckbox_flat {
        position: relative;
        margin-right: 5px;
    }

    #columnCheck {
        position: absolute;
        opacity: 0;
        margin-left: 15px;
    }

    #iCheck-helper {
        position: absolute;
        top: 0%;
        left: 0%;
        display: block;
        width: 100%;
        height: 100%;
        margin: 0px;
        padding: 0px;
        background: rgb(255, 255, 255);
        border: 0px;
        opacity: 0;
    }

    #make_deal_div {
        margin-top: 2em;
    }

    .adsImage {
        width: 100%;
        height: 100%;
        border-radius: 7px;
    }

    .adsContainer {
        background: #F0F0F0;
        padding: 28px;
        border-radius: 10px;
        box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16);
        margin-bottom: 2em;
    }

    @media (max-width: 1023px) {
        .select2 {
            max-width: 355px;
        }
    }
</style>


<div class="mt-5">
    <div class="row">
        <div class="col-10 col-md-10 col-lg-8 col-xl-8 offset-1 offset-md-1 offset-lg-2 offset-xl-2 adsContainer">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-6 deal_detail_img">
                    <img src="{{$advertise->advertise_image_url}}" class="adsImage" alt="{{ $advertise->title }} "/>
                </div>
                <div class="col-lg-6 col-md-6 deal_detail_box">
                    <h2 class="title">{{ $advertise->title }}</h2>
                    <p class="description">{{ $advertise->description }}</p>
                    {{--                    <a href="{{route('front.vendorPage',$service->company->slug)}}"><h3 class="mt-lg-1 mt-4">{{ $service->company->company_name }}</h3></a>--}}

                    <div class="deal_detail_contact mt-1">
                        <h6 class="info"><i class="fa fa-dot-circle-o mr-2"></i>{{ $advertise->info1 }}</h6>
                        <h6 class="info">@if(!is_null($advertise->info2))<i
                                class="fa fa-dot-circle-o mr-2"></i>{{ $advertise->info2 }}@endif</h6>
                        <h6 class="info">@if(!is_null($advertise->info3))<i
                                class="fa fa-dot-circle-o mr-2"></i>{{ $advertise->info3 }}@endif</h6>
                    </div>
                    <div class="priceContainer {{$advertise->price == 0 ? 'd-none' : ''}}">
                        <p class="badge badge-danger text-white text-bold mt-0 price">{{ $advertise->formated_price }}</p>
                    </div>

                    <div class="call-to-action">
                        <a href="{{$advertise->link}}"
                           class="btn btn-dark btn-block">{{ $advertise->call_to_action }}</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div
            class="col-10 col-md-6 col-lg-4 col-xl-6 offset-1 offset-md-3 offset-lg-4 offset-xl-3 bg-gray-light p-3 shadow-2"
            style="border-radius: 10px">
            <div class="row text-center shadow-sm p-2 mb-5 bg-white rounded" style="border-radius: 10px">
                <div class="col-md-12">
                    <h4>{{__('app.advertise')}}</h4>
                </div>
            </div>
            <div class="row mt-3">
                @if($advertise->ads_in_all_category == 'yes')
                    <input type="hidden" name="ads_in_all_category" id="ads_in_all_category"
                           value="{{$advertise->ads_in_all_category}}">
                    <div class="col-md-12">
                        <label id="label">{{__('app.advertiseAcrossThisCategory')}}</label>
                        <div class="form-group">
                            <input type="text" class="form-control" readonly name="category_id" id="category_id"
                                   value="{{$advertise->category->name}}">
                        </div>
                    </div>
                @else
                    <div class="col-md-12">
                        <label id="label">{{__('app.advertiseInArticle')}}</label>
                        <div class="form-group">
                            <input type="text" class="form-control" readonly name="article_id" id="article_id"
                                   value="{{$advertise->article->title}}">
                        </div>
                    </div>
                @endif
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label id="label">{{__('app.advertise')}} {{__('app.from')}}</label>

                    <div class="form-group">
                        <input type="text" class="form-control" readonly name="from" id="from"
                               placeholder="@lang('app.startDate')" value="{{$advertise->from}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <label id="label"> {{__('app.to')}}</label>

                    <div class="form-group">
                        <input type="text" class="form-control" readonly name="to" id="to"
                               placeholder="@lang('app.endDate')" value="{{$advertise->to}}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('app.amount')}}</label>
                        <input type="text" class="form-control" readonly name="amount"
                               id="amount" value="{{$advertise->formated_amount_to_pay}}"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>{{__('app.averageDaily')}}</label>
                        <input type="text" class="form-control" name="avg_amount" readonly id="avg_amount"
                               value="{{$advertise->formated_avg_amount_to_pay}}">
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-10 col-md-6 col-lg-4 col-xl-6 offset-1 offset-md-3 offset-lg-4 offset-xl-3">
            <div class="row">
                <div class="col-md-6 col-12 mb-3 pb-lg-0 pb-md-0">
                    <a class="payment_icon_name" href="{{route('admin.advertises.create')}}" id="back">
                        <div class="payment_icon_box">
                        <span>
                            <i class="fa fa-backward"></i>
                        </span>
                        </div>
                        <span class="payment_name">{{__('app.goBack')}}</span>
                    </a>
                </div>
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
            </div>
        </div>
    </div>


    <script src="{{ asset('front/js/bootstrap-datepicker.min.js') }}"></script>
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
                    data: {"_token" : "{{ csrf_token() }}", 'advertise_id' : {{$advertise->id }}, 'return_url' : 'advertises'},
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
        adsDays = 0;

        function validateURL(textval) {
            var regex = /^(https?:\/\/)?[a-z0-9-]*\.?[a-z0-9-]+\.[a-z0-9-]+(\/[^<>]*)?$/;
            return regex.test(textval);
        }

        function countChar(val) {
            var len = val.value.length;
            val.value = val.value.substring(0, 500);
            $('#charNum').text(len);

        }

        function calculateAdsDuration() {
            var from = moment($("#from").val(), 'YYYY-MM-DD')
            var to = moment($("#to").val(), 'YYYY-MM-DD')
            var diff = to.diff(from);
            var amount = $("#amount").val()
            diff = moment(diff).format('D');
            adsDays = diff = parseInt(diff)

            if (diff < 3) {
                $.showToastr("{{__('errors.adsAtLeast3Days')}}", 'error');
            }
            if (amount !== 0) {
                calculateAvgAmount()
            }
        }

        function calculateAvgAmount() {
            var amount = $("#amount").val()
            var numDays = adsDays
            var avg_amount = amount / numDays
            $("#avg_amount").val(avg_amount)
            console.log(amount, numDays);
        }

        $(document).ready(function () {

            $("#link").bind('blur',
                function () {
                    if (validateURL($(this).val())) {
                        $('.invalid-link').html('')
                    } else {
                        $('.invalid-link').html('({{__('errors.invalidLink')}})')
                        $.showToastr("{{__('errors.invalidLink')}}", 'error');
                    }
                }
            )

            $("#from").change(function () {
                calculateAdsDuration()
            })

            $("#to").change(function () {
                calculateAdsDuration()
            })

            $("#amount").change(function () {
                calculateAvgAmount()
            })
        });

        $('.datepicker').datepicker({
            templates: {
                leftArrow: '<i class="fa fa-chevron-left"></i>',
                rightArrow: '<i class="fa fa-chevron-right"></i>'
            },
            startDate: '-0d',
            language: '{{ $locale }}',
            weekStart: 0,
            format: "yyyy-mm-dd"
        });

        $('.dropify').dropify({
            messages: {
                default: '@lang("app.dragDrop")',
                replace: '@lang("app.dragDropReplace")',
                remove: '@lang("app.remove")',
                error: '@lang('app.largeFile')'
            }
        });

        $('#ads_in_all_category').change(function () {
            if ($(this).val() == "no") {
                $("#article_div").removeClass('d-none');
                $("#category_div").addClass('d-none');
            } else {
                $("#category_div").removeClass('d-none');
                $("#article_div").addClass('d-none');
            }
        });

        $(document).ready(function () {
            $("#article_div").addClass('d-none');
        })

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            return !(charCode > 31 && (charCode < 48 || charCode > 57));

        }


        $('body').on('click', '#submit-form', function () {
            $.easyAjax({
                url: '{{route('admin.advertises.store')}}',
                container: '#createForm',
                type: "POST",
                file: true,
                formReset: false,
                data: {data: $('#createForm').serialize()},
                success: function (response) {
                    console.log(response);
                },
                error: function (error) {
                    console.log(error);
                    if (error.status === 422) {
                        var data = error.responseJSON.errors
                    }
                    $.each(data, function (key, value) {
                        $.showToastr(value[0], 'error');
                    });
                }
            })
        });


    </script>
{{--    @include("partials.currency_format")--}}

