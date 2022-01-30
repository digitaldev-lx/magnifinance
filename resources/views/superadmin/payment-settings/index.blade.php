@extends('layouts.master')

@push('head-css')
    <style>
        #captcha-detail-modal .modal-dialog {
            height: 90%;
            max-width: 100%;
        }

        #captcha-detail-modal .modal-content {
            width: 600px;
            margin: 0 auto;
        }

        .modal.show {
            padding-right: 0px !important;
        }

        .d-none {
            display: none;
        }

    </style>
@endpush

@section('content')

    <div class="row">
        <div class="col-12 col-md-2 mb-4 mt-3 mb-md-0 mt-md-0">
            <a class="nav-link mb-2" href="{{ route('superadmin.settings.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> @lang('app.back')
            </a>
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link active" href="#online" data-toggle="tab">@lang('app.paymentCredential')
                    @lang('menu.settings')</a>
                <a class="nav-link" href="#offline" data-toggle="tab">@lang('app.offlinePaymentMethod')</a>
            </div>
        </div>
        <div class="col-12 col-md-10">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="tab-content">
                                <div class="active tab-pane" id="online">
                                    <h4>@lang('app.paymentCredential') @lang('menu.settings')</h4>
                                    <br>
                                    <form class="form-horizontal ajax-form" id="payment-form" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-12 ">
                                                <div class="row">
                                                    <div class="col-md">
                                                        <h5 class="text-secondary">@lang('app.showPaymentOptions')</h5>
                                                        <div class="form-group">
                                                            <label
                                                                class="control-label">@lang("modules.paymentCredential.allowCustomerPayment")</label>
                                                            <br>
                                                            <label class="switch">
                                                                <input type="checkbox" value="show"
                                                                    name="show_payment_options" @if ($credentialSetting->show_payment_options == 'show') checked @endif
                                                                    class="show_payment_options" id="show_payment_options">
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="col-md showPaymentOptions {{ $credentialSetting->show_payment_options != 'show' ? 'd-none' : '' }}">
                                                        <h5 class="text-primary">@lang('app.offlinePaymentMethod')</h5>
                                                        <div class="form-group">
                                                            <label
                                                                class="control-label">@lang("modules.paymentCredential.allowOfflinePayment")</label>
                                                            <br>
                                                            <label class="switch">
                                                                <input type="checkbox" name="" @if ($credentialSetting->offline_payment == 1) checked @endif
                                                                    class="offline-payment">
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <br>
                                                <div
                                                    class="showPaymentOptions {{ $credentialSetting->show_payment_options != 'show' ? 'd-none' : '' }}">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h5 class="text-warning">@lang('app.stripeCredential') </h5>
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    @lang("modules.paymentCredential.stripeCredentialStatus")
                                                                </label>
                                                                <br>
                                                                <label class="switch">
                                                                    <input type="checkbox" name="stripe_status"
                                                                        id="stripe_status" class="toggle-stripe"
                                                                        data-div-id="stripe-credentials" @if ($credentialSetting->stripe_status == 'active') checked @endif
                                                                        value="active">
                                                                    <span class="slider round"></span>
                                                                </label>
                                                                <input type="hidden" name="offline_payment" @if ($credentialSetting->offline_payment == 1) value="1"
                                                @else value="0" @endif id="offlinePayment">
                                                            </div>
                                                        </div>
                                                        <div id="stripe_commision_section"
                                                            class="col-md-6 float-right @if ($credentialSetting->stripe_status == 'deactive') d-none @endif">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h5 class="text-warning">@lang('app.add')
                                                                        @lang('app.commission')</h5>
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            @lang("modules.commission.status")
                                                                        </label>
                                                                        <br>
                                                                        <label class="switch">
                                                                            <input type="checkbox"
                                                                                name="stripe_commission_status"
                                                                                id="stripe_commission_status" @if ($credentialSetting->stripe_status == 'active' && $credentialSetting->stripe_commission_status == 'active') checked @endif
                                                                                value="active"
                                                                                data-element-id="stripe-commission-credentials"
                                                                                class="commission_status">
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group @if ($credentialSetting->stripe_commission_status ==
                                                                        'deactive') d-none @endif"
                                                                        id="stripe-commission-credentials">
                                                                        <label class="control-label">@lang('app.commission')
                                                                            @lang('app.percentage')</label>
                                                                        <input type="number"
                                                                            onkeypress="return isNumberKey(event)"
                                                                            class="form-control form-control-lg"
                                                                            id="stripeCommmissionAmt"
                                                                            name="stripeCommmissionAmt"
                                                                            value="{{ $credentialSetting->stripe_commission_percentage }}"
                                                                            min="0">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="stripe-credentials" class="@if ($credentialSetting->stripe_status == 'deactive') d-none @endif">
                                                        <div class="form-group">
                                                            <label>@lang("modules.paymentCredential.stripelClientID")</label>
                                                            <input type="text" name="stripe_client_id" id="stripe_client_id"
                                                                class="form-control form-control-lg"
                                                                value="{{ $credentialSetting->stripe_client_id }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>@lang("modules.paymentCredential.stripeSecret")</label>
                                                            <input type="password" name="stripe_secret" id="stripe_secret"
                                                                class="form-control form-control-lg"
                                                                value="{{ $credentialSetting->stripe_secret }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>@lang("modules.paymentCredential.stripeWebhookSecret")</label>
                                                            <input type="password" name="stripe_webhook_secret"
                                                                id="stripe_webhook_secret"
                                                                class="form-control form-control-lg"
                                                                value="{{ $credentialSetting->stripe_webhook_secret }}">
                                                        </div>
                                                        <div class=""><p class="text-primary"> @lang('app.webHookUrl'):-  {{ route('save_webhook') }}</p></div>
                                                    </div>
                                                    <hr>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h5 class="text-success">@lang('app.razorpayCredential') </h5>
                                                            <div class="form-group d-flex flex-column">
                                                                <label class="control-label">
                                                                    @lang("modules.paymentCredential.razorpayCredentialStatus")
                                                                </label>
                                                                <div class="d-flex">
                                                                    <label class="switch mr-2">
                                                                        <input type="checkbox" name="razorpay_status"
                                                                            id="razorpay_status" class="toggle-razorpay"
                                                                            data-div-id="razorpay-credentials" @if ($credentialSetting->razorpay_status == 'active') checked @endif
                                                                            value="active">
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                    <span class="text-danger wrong-currency-message">
                                                                        @lang('modules.paymentCredential.changeCurrencyToINR')(<a
                                                                            href="{{ route('superadmin.settings.index') . '#general' }}">@lang('menu.general')</a>)
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div id="razorpay_commision_section"
                                                            class="col-md-6 float-right @if ($credentialSetting->razorpay_status == 'deactive') d-none @endif">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h5 class="text-success">@lang('app.add')
                                                                        @lang('app.commission')</h5>
                                                                    <div class="form-group">
                                                                        <label class="control-label">
                                                                            @lang("modules.commission.status")
                                                                        </label>
                                                                        <br>
                                                                        <label class="switch">
                                                                            <input type="checkbox"
                                                                                name="razorpay_commission_status"
                                                                                id="razorpay_commission_status" @if ($credentialSetting->razorpay_commission_status == 'active') checked @endif
                                                                                value="active"
                                                                                data-element-id="razorpay-commission-credentials"
                                                                                class="commission_status">
                                                                            <span class="slider round"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group @if ($credentialSetting->razorpay_status == 'deactive'
                                                                        && $credentialSetting->razorpay_commission_status ==
                                                                        'deactive') d-none @endif"
                                                                        id="razorpay-commission-credentials">
                                                                        <label class="control-label">@lang('app.commission')
                                                                            @lang('app.percentage')</label>
                                                                        <input type="number"
                                                                            onkeypress="return isNumberKey(event)"
                                                                            class="form-control form-control-lg"
                                                                            id="razorCommmissionAmt"
                                                                            name="razorCommmissionAmt"
                                                                            value="{{ $credentialSetting->razorpay_commission_percentage }}"
                                                                            min="0">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="razorpay-credentials" class="@if ($credentialSetting->razorpay_status == 'deactive') d-none @endif">
                                                        <div class="form-group">
                                                            <label>@lang("modules.paymentCredential.razorpayKey")</label>
                                                            <input type="text" name="razorpay_key" id="razorpay_key"
                                                                class="form-control form-control-lg"
                                                                value="{{ $credentialSetting->razorpay_key }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>@lang("modules.paymentCredential.razorpaySecret")</label>
                                                            <input type="password" name="razorpay_secret"
                                                                id="razorpay_secret" class="form-control form-control-lg"
                                                                value="{{ $credentialSetting->razorpay_secret }}">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>@lang("modules.paymentCredential.razorpayWebhookSecret")</label>
                                                            <input type="password" name="razorpay_webhook_secret"
                                                                id="razorpay_webhook_secret"
                                                                class="form-control form-control-lg"
                                                                value="{{ $credentialSetting->razorpay_webhook_secret }}">
                                                        </div>
                                                        <div class=""><p class="text-primary"> @lang('app.webHookUrl'):- {{ route('save_razorpay-webhook') }}</p></div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button id="save-payment" type="button" class="btn btn-success"><i
                                                            class="fa fa-check"></i> @lang('app.save')</button>
                                                </div>
                                            </div>
                                            <!--/span-->
                                        </div>
                                    </form>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="offline">
                                    @include('superadmin.payment-settings.offline_payments')
                                </div>
                                <!-- /.tab-pane -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- coupon detail Modal --}}
    <div class="modal fade bs-modal-lg in" id="captcha-detail-modal" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" id="modal-data-application">
            <div class="modal-content">
                <div id="modelHeading"></div>
            </div>
        </div>
    </div>
    {{-- coupon detail Modal Ends --}}

@endsection

@push('footer-js')
<script>
    $(function () {
        $('.wrong-currency-message').hide();

            $('body').on('change', '#stripe_status', function() {
                if ($(this).is(':checked')) {
                    $('#stripe-credentials').removeClass('d-none')
                } else {
                    $('#stripe-credentials').addClass('d-none')
                }
            });

        $('body').on('change', '#stripe_commission_status', function() {
            if ($(this).is(':checked')) {
                $('#stripe-commission-credentials').removeClass('d-none');
            } else {
                $('#stripeCommmissionAmt').val('');
                $('#stripe-commission-credentials').addClass('d-none');
            }
        });

        $('body').on('change', '#razorpay_status', function() {
            if ($(this).is(':checked')) {
                $('#razorpay-credentials').removeClass('d-none')
            } else {
                $('#razorpay-credentials').addClass('d-none')
            }
        });

        $('body').on('change', '#razorpay_commission_status', function() {
            if ($(this).is(':checked')) {
                $('#razorpay-commission-credentials').removeClass('d-none');
            } else {
                $('#razorpay-commission-credentials').addClass('d-none');
                $('#razorCommmissionAmt').val('');
            }
        });

        $('body').on('change', '#show_payment_options', function() {
            if ($(this).is(':checked')) {
                $('.showPaymentOptions').removeClass('d-none');
            } else {
                $('.showPaymentOptions').addClass('d-none');
            }
        });
    });

        // store the currently selected tab in the hash value
        $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
            var id = $(e.target).attr("href").substr(1);
            window.location.hash = id;
        });

        // on load of the page: switch to the currently selected tab
        var hash = window.location.hash;
        $('#v-pills-tab a[href="' + hash + '"]').tab('show');

        $('body').on('change', '.offline-payment', function() {
            $(this).is(':checked') ? $('#offlinePayment').val(1) : $('#offlinePayment').val(0);
        });



    function checkCurrencyCode(currency_code) {
        return currency_code === 'INR' ? true : false;
    }

    $('body').on('click', '.commission_status', function() {
        let elementId = $(this).data('element-id');
        toggle('#'+elementId);
    });

    function toggle(elementBox) {
        var elBox = $(elementBox);
        elBox.slideToggle();
    }

    $('body').on('click', '.toggle-stripe', function() {
        let divId = $(this).data('div-id');
        toggleStripe('#'+divId);
    });

    function toggleStripe(el) {
        toggle(el)
        $('#stripe_commision_section').hasClass('d-none') ? $('#stripe_commision_section').removeClass('d-none') : $('#stripe_commision_section').addClass('d-none')
    }

    $('body').on('click', '.toggle-razorpay', function() {
        let divId = $(this).data('div-id');
        toggleRazorPay('#'+divId);
    });

    function toggleRazorPay(elementBox) {
        var elBox = $(elementBox);
        if (checkCurrencyCode('{{ $settings->currency->currency_code }}')) {
            elBox.slideToggle();
            $('.wrong-currency-message').fadeOut();
            $('#razorpay_commision_section').hasClass('d-none') ? $('#razorpay_commision_section').removeClass('d-none') : $('#razorpay_commision_section').addClass('d-none')
        }
        else {
            $('.wrong-currency-message').fadeIn();
            $('#razorpay_status').prop('checked', false);
        }
    }

    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
        return true;
    }

    var table;
    $(document).ready(function() {
        // pages table
        table = $('#myTable').dataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: '{!! route('superadmin.payment-settings.offlinePayments') !!}',
            language: languageOptions(),
            "fnDrawCallback": function( oSettings ) {
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            },
            order: [[0, 'DESC']],
            columns: [
                { data: 'DT_RowIndex'},
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', width: '20%' }
            ]
        });
        new $.fn.dataTable.FixedHeader( table );

        $('body').on('click', '.edit-payment-method', function () {
            var id = $(this).data('row-id');
            var url = '{{ route('superadmin.payment-settings.edit', ':id')}}';
            url = url.replace(':id', id);

                $(modal_lg + ' ' + modal_heading).html('@lang('app.edit') @lang('app.offlinePaymentMethod')');
                $.ajaxModal(modal_lg, url);
            });

        $('body').on('click', '#create-payment-method', function () {
            var url = '{{ route('superadmin.payment-settings.create') }}';

                $(modal_lg + ' ' + modal_heading).html('@lang('app.createNew') @lang('app.offlinePaymentMethod')');
                $.ajaxModal(modal_lg, url);
            });

        $('body').on('click', '.delete-row', function(){
            var id = $(this).data('row-id');
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            }).then((willDelete) => {
                if (willDelete) {
                    var url = "{{ route('superadmin.payment-settings.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        data: {'_token': token, '_method': 'DELETE'},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                table._fnDraw();
                            }
                        }
                    });
                }
            });
        });

        $('body').on('click', '#save-payment', function() {
            $.easyAjax({
                url: '{{route('superadmin.credential.update', $credentialSetting->id)}}',
                container: '#payment-form',
                type: "POST",
                data: $('#payment-form').serialize(),
                success: function (response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            })
        });
    });

</script>
@endpush
