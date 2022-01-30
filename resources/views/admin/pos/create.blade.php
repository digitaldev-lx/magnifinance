@extends('layouts.master')

@push('head-css')
<style>
    .coupons-base-content .fa-tag {
        font-size: 20px;
        color: #222;
    }
    .coupons-base-content p {
        color: #3289da;
        font-size: 11px;
    }
    .remove-button {
        margin-bottom: 4px;
        margin-left: 3px;
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
    .availableEmp {
        background-color: transparent;
        color: #17a2b8;
        font-size: 15px;
    }
    /* Popover */
    .popover {
        background: rgb(61, 59, 59);
        border: 2px solid rgb(19, 18, 18);
    }
    /* Popover Header */
    .popover-header {
        background-color: rgb(198, 230, 236);
        color: rgb(8, 8, 8);
        font-size: 15px;
        text-align: center;
    }
    /* Popover Body */
    .popover-body {
        background-color: #323534;
        color: #f7f2f2;
        padding-left: 10px;
        padding-top: 10px;
        font-size: 15px;
    }
    /* Popover Arrow */
    .bs-popover-left .arrow::after,
    .bs-popover-auto[x-placement^="left"] .arrow::after {
        right: 1px;
        border-left-color: rgb(0, 0, 0);
    }
    #coupon {
        width: 80%;
        display: inline;
    }
    #applyCoupon {
        margin-bottom: 4px;
        margin-left: 3px;
    }
    .d-none {
        display:none;
    }
</style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-7">
            <form id="filter-form" class="ajax-form" method="GET">
                <div class="card card-light">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-4">
                                <label class="col-sm-12 col-form-label">@lang('app.product') / @lang('app.service') @lang('app.filter') </label>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <select id="type-filter" name="type_id" class="form-control">
                                            <option value="">@lang('app.service')</option>
                                            <option value="1">@lang('app.product')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 categoryFilter">
                                <label class="col-sm-12 col-form-label">@lang('app.category') @lang('app.filter')</label>
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <select id="category-filter" name="category_id" class="form-control">
                                            <option value="0">--</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group row">
                                    <label class="col-sm-12 col-form-label">@lang('app.location') @lang('app.filter')</label>
                                    <div class="col-sm-12">
                                        <select id="location-filter" name="location_id" class="form-control">
                                            @foreach($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                            <div class="input-group">
                            <input type="text" id="posSearch"  name="search_key" class="form-control" placeholder="@lang('modules.search.pos')">
                            </div>
                        </div>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" id="pos-services">
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </form>
        </div>
        <div class="col-md-5">
            <form id="pos-form" class="ajax-form" method="POST" autocomplete="off">
                @csrf
                <div class="card card-dark">
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">

                            <input type="hidden" class="form-control" name="posTime" id="posTime" value="">

                            <div class="col-md-10">
                                <label for="">@lang('app.date')</label>
                                <div class="input-group form-group">

                                    <input type="text" class="form-control posDateTime" name="date" id="datepicker" value="">
                                    <span class="input-group-append input-group-addon">
                                        <button type="button" class="btn btn-info"><span class="fa fa-calendar-o"></span></button>
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-10">
                                <label for="">@lang('app.time')</label>
                                <div class="input-group form-group">

                                    <input type="text" class="form-control posDateTime" name="time" id="timepicker" value="">
                                    <span class="input-group-append input-group-addon">
                                        <button type="button" class="btn btn-info"><span class="fa fa-clock-o"></span></button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="">@lang('modules.booking.searchNote')</label>
                                    <select id="user_id" name="user_id" class="form-control select2"></select>
                                    <div id="user-error" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mt-2">&nbsp;</div>
                                <button class="btn btn-success btn-rounded" id="select-customer" type="button"><i
                                            class="fa fa-plus"></i> @lang('app.add')</button>
                            </div>

                            <div class="col-md-10" id="employee_list">
                                <div class="form-group">
                                    <label for="">@lang('modules.booking.assignEmployee')</label>
                                    <select id="employee" name="employee[]" class="form-control select2" multiple="multiple">
                                        @foreach($employees as $employee)
                                            <option
                                            value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                    <div id="employee-error" class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="mt-3">&nbsp;</div>
                                <span class="fa fa-users availableEmp" data-trigger="hover" data-toggle="popover"
                                    data-html="true" title="@lang('modules.booking.followingEmployeesAreAvailable') :"
                                    data-content=""></span>
                            </div>

                            <div class="col-md-12 mt-2 mb-2 p-2" id="pos-customer-details"></div>

                        </div>

                        <div class="row">
                            <table class="table table-condensed" id="cart-table">
                                <thead>
                                    <tr>
                                        <th colspan="2" width="30%">@lang('app.service')</th>
                                        <th width="20%">@lang('app.price')</th>
                                        <th width="23%">@lang('app.quantity')</th>
                                        <th class="text-right">@lang('app.subTotal')</th>
                                        <th><i class="fa fa-gear"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="no-service">
                                        <td colspan="5" class="text-center text-danger">@lang("messages.selectService")</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <table class="table table-condensed" id="product-table">
                                <thead>
                                    <tr>
                                        <th colspan="2" width="30%">@lang('app.product')</th>
                                        <th width="20%">@lang('app.price')</th>
                                        <th width="23%">@lang('app.quantity')</th>
                                        <th class="text-right">@lang('app.subTotal')</th>
                                        <th><i class="fa fa-gear"></i></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="no-product">
                                        <td colspan="5" class="text-center text-danger">@lang("messages.selectProduct")</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <!-- /.card-body -->

                </div>
                <!-- /.card -->

                <div class="card">
                    <div class="card-body">
                        <div class="row pos-calculations">
                            <div class="col-md-6 border-bottom">
                                @lang('app.serviceSubTotal')
                            </div>
                            <div class="col-md-6 border-bottom" id="cart-sub-total">
                                {{ currencyFormatter(0,myCurrencySymbol())}}
                            </div>
                            <div class="col-md-6 border-bottom">
                                <h6>@lang('app.discount') (%)</h6>
                            </div>
                            <div class="col-md-6 border-bottom">
                                <input onchange="checkValue(this.value)" onkeypress="return isNumberKey(event)" type="number" id="cart-discount" name="cart_discount" class="form-control" step=".01" min="0" max="100" value="0">
                            </div>
                            <div class="col-md-6 border-bottom">
                                <h6>@lang('app.totalTax')</h6>
                            </div>
                            <div class="col-md-6 border-bottom">
                                <h5 id="cart-tax-amount">{{ currencyFormatter(0,myCurrencySymbol())}}</h5>
                            </div>
                            <div class="col-md-12 border-bottom" id="applyCouponBox">
                                <div class="row">
                                    <div class="col-md-6 ">
                                        <h6>@lang('app.applyCoupon')</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" id="coupon" name="coupon" class="form-control">
                                        <button type="button" id="applyCoupon" class="btn btn-success "><i class="fa fa-check"></i> </button>
                                    </div>
                                </div>

                            </div>


                            <div class="col-md-12 py-3 border-bottom d-none" id="removeCouponBox">
                                <h5>@lang('app.coupons')</h5>

                                <div class="coupons-base-content justify-content-between d-flex align-items-center">
                                   <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <i class="fa fa-tag"></i>
                                        </div>
                                        <div>
                                            <h5 class="coupons-name mb-0" id="couponCode"> </h5>
                                            <p class="mb-0 text-success">
                                                @lang('app.youSaved') <span id="couponCodeAmonut"> </span>
                                            </p>
                                        </div>
                                   </div>
                                    <div>
                                        <button type="button" class="btn btn-success btn-outline-danger remove-button"> @lang('app.remove') </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 border-bottom">
                                @lang('app.service') @lang('app.total')
                            </div>
                            <div class="col-md-6 border-bottom" id="cart-total">
                                {{ currencyFormatter(0,myCurrencySymbol()) }}
                            </div>

                            <div class="col-md-6 border-bottom">
                                @lang('app.product') @lang('app.total')
                            </div>
                            <div class="col-md-6 border-bottom" id="product-sub-total">
                                {{ currencyFormatter(0,myCurrencySymbol()) }}
                            </div>

                            <div class="col-md-6" id="totalAmountBox">
                                <h4>@lang('app.total')</h4>
                            </div>
                            <div class="col-md-6">
                                <h4 id="total-cart"> {{ currencyFormatter(0,myCurrencySymbol()) }}</h4>
                                <input type="hidden" id="cart-total-input">
                                <input type="hidden" id="coupon_id" name="coupon_id" >
                                <input type="hidden" id="coupon_amount" name="coupon_amount" >
                            </div>

                            <div class="col-md-6 mt-2">
                                <button type="button" id="empty-cart" class="btn btn-danger p-3 btn-lg btn-block">@lang('modules.booking.emptyCart')</button>
                                <div id="cart-item-error" class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 mt-2">
                                <button type="button" id="do-payment" class="btn btn-success p-3 btn-lg btn-block">@lang('app.pay')</button>
                                <div id="cart-item-error" class="invalid-feedback"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('footer-js')
    <script src="{{ asset('assets/plugins/select2/dist/js/i18n/'.$settings->locale.'.js') }}"></script>
    <script>

        var currentTime = moment().format("hh:mm A");
        var globalCartTotal = 0;
        var couponAmount = 0;
        var couponCodeValue = '';
        var couponApplied = false;
        var moment_Date = moment();
        var pos_date = moment_Date.format('YYYY-MM-DD');
        var pos_time = moment_Date.format("hh:mm a");
        var cartServicesData = [];

        $('#posTime').val(moment_Date.format("hh:mm a"));

        $('#timepicker').val(currentTime);

        $('#timepicker').datetimepicker({
            format: '{{ $time_picker_format }}',
            locale: '{{ $settings->locale }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
            useCurrent: false,
        }).on('dp.change', function(e) {
            $('#posTime').val(convert(e.date));
        });

        $('#datepicker').datetimepicker({
            format: '{{ $date_picker_format }}',
            locale: '{{ $settings->locale }}',
            defaultDate: moment_Date,
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right"
            }
        }).on('dp.change', function(e) {
            pos_date =  moment(e.date).format('YYYY-MM-DD');
        });

        $(document).ready(function() {
            $('[data-toggle="popover"]').popover();

            /* to show default location on POS */
            filterServices();
        });

        function convert(str) {
            var date = new Date(str);
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            hours = ("0" + hours).slice(-2);
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        }

        /* Available employee suggestion in pos */
        $('body').on('dp.change', '.posDateTime', function() {
            if (cartServicesData.length == 0) {
                return $.showToastr("@lang('errors.coupon.serviceRequired')", 'error');
            }
            fetch_employees();
        });

        function fetch_employees() {

            let time = $('#posTime').val();

            if (pos_date !== null && time !== null && cartServicesData.length > 0) {
                $.easyAjax({
                    url: '{{ route('admin.pos.check-user-availability') }}',
                    type: 'POST',
                    data: {
                        'date': pos_date,
                        'time': time,
                        'cart_services_data': cartServicesData,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.continue_booking == 'no') {
                            $(".availableEmp").attr("data-content",
                                "@lang('modules.booking.sorry')! @lang('modules.booking.NoEmployeeAvailable')."
                            );
                        } else {
                            var employees = '';
                            if (response.availableEmp.length > 0) {
                                response.availableEmp.forEach(employee => {
                                    employees += `<ul>
                                        <li>
                                            ${employee.name}
                                        </li>
                                    </ul>`;
                                });
                            }
                            $(".availableEmp").attr("data-content", employees);
                        }
                    }
                });
            } else if(cartServicesData.length == 0) {
                $(".availableEmp").attr("data-content",
                    "@lang('modules.booking.sorry')! @lang('modules.booking.NoEmployeeAvailable').");
            }
        }

        function removeCoupon () {
            couponApplied = false;
            $('#coupon').val('');
            $('#coupon_id').val('');
            $('#coupon_amount').val(0);
            couponAmount = 0;
            calculateTotal();

            $('.couponDiscountBox').remove();
            $('#removeCouponBox').removeClass('d-none');
            $('#applyCouponBox').show();
        }

        $('body').on('click', '#applyCoupon', function() {

            let cartTotal = 0;
            let cartSubTotal = 0;
            let cartDiscount = $('#cart-discount').val();
            let discount = 0;
            let tax = 0;

            $("input[name='cart_prices[]']").each(function( index ) {
                let servicePrice = $(this).val();
                let qty = $("input[name='cart_quantity[]']").eq(index).val();
                cartSubTotal = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));

                let taxPercent = $("input[name='tax_percent[]']").eq(index).val();
                taxAmount = ( (parseFloat(taxPercent)/100)* ((parseFloat(servicePrice) * parseInt(qty))));
                tax +=parseFloat(taxAmount);
            });

            $("#cart-sub-total").html(currency_format(cartSubTotal.toFixed(2)));

            if(parseFloat(cartDiscount) > 0){
                if(cartDiscount > 100) cartDiscount = 100;

                discount = ((parseFloat(cartDiscount)/100)*cartSubTotal);
            }

            cartSubTotal = (cartSubTotal - discount).toFixed(2);

            $('#cart-tax-amount').html(currency_format(tax.toFixed(2)))

            cartTotal = (parseFloat(cartSubTotal) + parseFloat(tax));


            var couponVal = $('#coupon').val();
            couponCodeValue = couponVal;
            var userID    = $('#user_id').val();
            var cart_discount    = $('#cart-discount').val();
            var cartServices = [];
            var titles = $('input[name^=cart_services]').map(function(idx, elem) {
                cartServices.push([$(elem).val(),$("input[name='cart_quantity[]']").eq(idx).val()]);
            }).get();

            if(cartServices === undefined || cartServices === "" || cartServices === null || cartServices.length <= 0){
                return $.showToastr("@lang('errors.coupon.serviceRequired')", 'error');
            }
            if(userID === undefined || userID === "" || userID === null){
                return $.showToastr("@lang('errors.coupon.customerRequired')", 'error');
            }
            if(couponVal === undefined || couponVal === "" || couponVal === null){
                return $.showToastr("@lang('errors.coupon.required')", 'error');
            } else {

                var token = '{{ csrf_token() }}';
                $.easyAjax({
                    url: '{{ route('admin.pos.apply-coupon') }}',
                    type: 'POST',
                    data: {'_token':token,'coupon':couponVal,'user_id':userID, 'cart_services': cartServices, 'cart_discount': cart_discount},
                    success: function (response) {
                        if(response.status == 'success') {
                            couponApplied = true;
                            couponAmount = response.amount;
                            if(couponAmount>cartTotal)
                            {
                                    couponAmount = cartTotal;
                            }
                            calculateTotal();
                            $('.couponDiscountBox').remove();
                            var discountElement =     '<div class="col-md-6 border-bottom couponDiscountBox" id="couponDiscountBox">'+
                                                        '<h6>@lang('app.couponDiscount')('+response.couponData.title+') </h6>'+
                                                        '</div>'+
                                                        '<div class="col-md-6 border-bottom couponDiscountBox">'+
                                                        ' <h5 id="coupon-discount-amount">-'+currency_format(couponAmount)+'</h5>'+
                                                        '</div>';

                            $(discountElement).insertBefore( "#totalAmountBox" );

                            $('#coupon_id').val(response.couponData.id);
                            $('#coupon_amount').val(couponAmount);

                            $('#applyCouponBox').hide();
                            $('#removeCouponBox').removeClass('d-none');

                            $('#couponCodeAmonut').html(currency_format(couponAmount));
                            $('#couponCode').html(response.couponData.title);
                        }
                    }
                })
            }

        });

        function updateCoupon () {
            if(couponApplied){

                var userID = $('#user_id').val();

                var cart_discount   = $('#cart-discount').val();
                var cartServices = [];
                var titles = $('input[name^=cart_services]').map(function(idx, elem) {
                    cartServices.push([$(elem).val(),$("input[name='cart_quantity[]']").eq(idx).val()]);
                }).get();

                if(cartServices === undefined || cartServices === "" || cartServices === null ||
                    cartServices.length <= 0 || userID === undefined || userID === "" || userID === null){
                    removeCoupon ();
                    return false;
                }
                if(couponCodeValue === undefined || couponCodeValue === "" || couponCodeValue === null){
                    removeCoupon ();
                    return false;
                }else{
                    var token = '{{ csrf_token() }}';
                    $.easyAjax({
                        url: '{{ route('admin.pos.update-coupon') }}',
                        type: 'POST',
                        data: {'_token':token,'coupon':couponCodeValue, 'cart_discount': cart_discount, 'cart_services': cartServices},
                        success: function (response) {
                            if(response.status != 'fail'){

                                couponAmount = response.amount;

                                $('.couponDiscountBox').remove();
                                var discountElement =     '<div class="col-md-6 border-bottom couponDiscountBox" id="couponDiscountBox">'+
                                                            '<h6>@lang('app.couponDiscount')('+response.couponData.title+') </h6>'+
                                                            '</div>'+
                                                            '<div class="col-md-6 border-bottom couponDiscountBox">'+
                                                            ' <h5 id="coupon-discount-amount">-'+currency_format(response.amount)+'</h5>'+
                                                            '</div>';

                                $(discountElement).insertBefore( "#totalAmountBox" );

                                $('#coupon_id').val(response.couponData.id);
                                $('#coupon_amount').val(response.amount);

                                $('#applyCouponBox').hide();
                                $('#removeCouponBox').removeClass('d-none');

                                $('#couponCodeAmonut').html(currency_format(response.amount));
                                $('#couponCode').html(response.couponData.title);
                                calculateTotal();
                            } else {
                                removeCoupon ();
                            }
                        }
                    })
                }
            }
        }

        $('#user_id').select2({
            language: '{{ $settings->locale }}',
            ajax: {
                url: "{{ route('admin.pos.search-customer') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            placeholder: "@lang('modules.booking.selectCustomer')",
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        }).on('select2:select', function (e) {
            var userId = $('#user_id').val();
            $('#user-error').text('');
            customerDetails(userId);
        });

        function formatRepo(repo) {
            if (repo.loading) {
                return repo.text;
            }

            var markup = "<div class='row'>" +
                "<div class='col-md-12'><h6>" + repo.full_name + "</h6></div>";

            markup += "<div class='col-md-6'><i class='fa fa-envelope'></i>: " + repo.email + "</div>" +
                "<div class='col-md-6'><i class='fa fa-phone'></i>: " + repo.mobile + "</div>" +
                "</div>";

            return markup;
        }

        function formatRepoSelection(repo) {
            return repo.full_name || repo.text;
        }

        $('body').on('click', '#select-customer', function() {
            var url = '{{ route('admin.pos.select-customer')}}';
            $(modal_default + ' ' + modal_heading).html('...');
            $.ajaxModal(modal_default, url);
        });

        var customerDetails = function(userId){
            let url = '{{route('admin.customers.show', ":id")}}';
            url = url.replace(":id", userId);

            $.easyAjax({
                url: url,
                type: "GET",
                success: function (response) {
                    if(response.status == 'success'){
                        $('#pos-customer-details').html(response.view);
                    }
                }
            })
        };

        $('body').on('change', '#type-filter, #category-filter, #location-filter', function() {
            posDataGet();
        });

        $('body').on('keyup', '#posSearch', function(e) {
            e.preventDefault();
            posDataGet();
        });

        $('body').on("keydown", "#filter-form", function(event) {
            return event.key != "Enter";
        });

        function posDataGet() {
            var type_id = $('#type-filter').val();
            if(type_id){
                filterProducts();
                $('.categoryFilter').hide();
            }else{
                filterServices();
                $('.categoryFilter').show();
            }
        }
        function filterServices() {
            $.easyAjax({
                url: '{{ route('admin.pos.filter-services') }}',
                type: 'GET',
                container: '#pos-services',
                data: $('#filter-form').serialize(),
                success: function (response) {
                    $('#pos-services').html(response.view);
                }
            })
        }

        function filterProducts() {
            $.easyAjax({
                url: '{{ route('admin.pos.filter-products') }}',
                type: 'GET',
                container: '#pos-services',
                data: $('#filter-form').serialize(),
                success: function (response) {
                    $('#pos-services').html(response.view);
                }
            })
        }

        $("body").on('click', '.add-to-cart', function () {
            let serviceId = $(this).data('service-id');
            let servicePrice = $(this).data('service-price');
            let serviceName = $(this).data('service-name');
            let taxPercent = $(this).data('total_tax_percent');

            let productId = $(this).data('product-id');
            let productPrice = $(this).data('product-price');
            let productName = $(this).data('product-name');

            let isAdded = checkExists(serviceId); //check if service already added to cart
            let Added = checkIfExists(productId); //check if product already added to cart

            if(isAdded === false && serviceId !== undefined){
                let cartRow =  '<tr>\n' +
                    '                                <td><input type="hidden" name="cart_services[]" value="'+serviceId+'">'+serviceName+'</td>\n' +
                    '                                <td><input type="hidden" name="tax_percent[]" value="'+taxPercent+'"></td>\n' +
                    '                                <td><input type="hidden" name="cart_prices[]" class="cart-price-'+serviceId+'" value="'+servicePrice+'">'+currency_format(servicePrice)+'</td>\n' +
                    '                                <td><div class="input-group">\n' +
                    '                  <div class="input-group-prepend">\n' +
                    '                    <button type="button" class="btn btn-default quantity-minus" data-service-id="'+serviceId+'"><i class="fa fa-minus"></i></button>\n' +
                    '                  </div>\n' +
                    '                  <input type="text" readonly name="cart_quantity[]" data-service-id="'+serviceId+'" class="form-control cart-service-'+serviceId+'" value="1">\n' +
                    '                  <div class="input-group-append">\n' +
                    '                    <button type="button" class="btn btn-default quantity-plus" data-service-id="'+serviceId+'"><i class="fa fa-plus"></i></button>\n' +
                    '                  </div>\n' +
                    '                </div></td>\n' +
                    '                                <td class="text-right cart-subtotal-'+serviceId+'">'+currency_format(servicePrice)+'</td>\n' +
                    '                                <td>\n' +
                    '                                    <a href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-cart-row" data-service-id="'+serviceId+'" data-toggle="tooltip"\n' +
                    '                                      data-original-title="@lang('app.delete')"><i class="fa fa-times"\n' +
                    '                                                                                                   aria-hidden="true"></i></a>\n' +
                    '                                </td>\n' +
                    '                            </tr>';

                if ($("#cart-table tbody").has('tr#no-service')) {
                    $("#cart-table tbody tr#no-service").remove();
                }
                $("#cart-table tbody").append(cartRow);
                $('#cart-item-error').text('');
                calculateTotal();
                updateCoupon ();

                /* Available employee suggestion in pos */
                cartServicesData.push(serviceId);
                fetch_employees();

            } else if(Added === false && productId !== undefined){
                let productRow =  '<tr>\n' +
                    '                                <td><input type="hidden" name="product_cart_services[]" value="'+productId+'">'+productName+'</td>\n' +
                    '                                <td><input type="hidden" name="product_tax_percent[]" value="'+taxPercent+'"></td>\n' +
                    '                                <td><input type="hidden" name="product_cart_prices[]" class="product-cart-price-'+productId+'" value="'+productPrice+'">'+currency_format(productPrice)+'</td>\n' +
                    '                                <td><div class="input-group">\n' +
                    '                  <div class="input-group-prepend">\n' +
                    '                    <button type="button" class="btn btn-default product-quantity-minus" data-product-id="'+productId+'"><i class="fa fa-minus"></i></button>\n' +
                    '                  </div>\n' +
                    '                  <input type="text" readonly name="product_cart_quantity[]" data-product-id="'+productId+'" class="form-control cart-product-'+productId+'" value="1">\n' +
                    '                  <div class="input-group-append">\n' +
                    '                    <button type="button" class="btn btn-default product-quantity-plus" data-product-id="'+productId+'"><i class="fa fa-plus"></i></button>\n' +
                    '                  </div>\n' +
                    '                </div></td>\n' +
                    '                                <td class="text-right product-subtotal-'+productId+'">'+currency_format(productPrice)+'</td>\n' +
                    '                                <td>\n' +
                    '                                    <a href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-product-row" data-product-id="'+productId+'" data-toggle="tooltip"\n' +
                    '                                      data-original-title="@lang('app.delete')"><i class="fa fa-times"\n' +
                    '                                                                                                   aria-hidden="true"></i></a>\n' +
                    '                                </td>\n' +
                    '                            </tr>';

                if ($("#product-table tbody").has('tr#no-product')) {
                    $("#product-table tbody tr#no-product").remove();
                }
                $("#product-table tbody").append(productRow);
                $('#product-item-error').text('');
                calculateProductTotal();

            }
        });

        $("#cart-table").on('change', "input[name='cart_quantity[]']", function () {
            let serviceId = $(this).data('service-id');
            let qty = $(this).val();

            updateCartQuantity(serviceId, qty);
        });

        $('#cart-table').on('click', '.quantity-minus', function () {
            let serviceId = $(this).data('service-id');

            let qty = $('.cart-service-'+serviceId).val();
            qty = parseInt(qty)-1;

            if(qty < 1){
                qty = 1;
            }
            $('.cart-service-'+serviceId).val(qty);

            updateCartQuantity(serviceId, qty);
        });

        $('#cart-table').on('click', '.quantity-plus', function () {
            let serviceId = $(this).data('service-id');

            let qty = $('.cart-service-'+serviceId).val();
            qty = parseInt(qty)+1;

            $('.cart-service-'+serviceId).val(qty);

            updateCartQuantity(serviceId, qty);
        });

        // Product
        $("#product-table").on('change', "input[name='product_cart_quantity[]']", function () {
            let productId = $(this).data('product-id');
            let qty = $(this).val();

            updateProductCartQuantity(productId, qty);
        });

        $('#product-table').on('click', '.product-quantity-minus', function () {
            let productId = $(this).data('product-id');

            let qty = $('.cart-product-'+productId).val();
            qty = parseInt(qty)-1;

            if(qty < 1){
                qty = 1;
            }
            $('.cart-product-'+productId).val(qty);

            updateProductCartQuantity(productId, qty);
        });

        $('#product-table').on('click', '.product-quantity-plus', function () {
            let productId = $(this).data('product-id');

            let qty = $('.cart-product-'+productId).val();
            qty = parseInt(qty)+1;

            $('.cart-product-'+productId).val(qty);

            updateProductCartQuantity(productId, qty);
        });

        function checkExists(serviceId) {
            let isAdded = $(".cart-service-"+serviceId).length;
            let qty = $(".cart-service-"+serviceId).val();
            qty = parseInt(qty)+1;

            $(".cart-service-"+serviceId).val(qty);

            if(isAdded > 0){
                return updateCartQuantity(serviceId, qty);
            }
            return false;
        }

        function checkIfExists(productId) {
            let Added = $(".cart-product-"+productId).length;
            let qty = $(".cart-product-"+productId).val();
            qty = parseInt(qty)+1;

            $(".cart-product-"+productId).val(qty);

            if(Added > 0){
                return updateProductCartQuantity(productId, qty);
            }
            return false;
        }

        function updateCartQuantity(serviceId, qty) {

            let servicePrice = $('.cart-price-'+serviceId).val();

            let subTotal = (parseFloat(servicePrice) * parseInt(qty));

            $('.cart-subtotal-'+serviceId).html(currency_format(subTotal.toFixed(2)));

            calculateTotal();
            updateCoupon ();
        }

        function updateProductCartQuantity(productId, qty) {

            let productPrice = $('.product-cart-price-'+productId).val();

            let subTotal = (parseFloat(productPrice) * parseInt(qty));

            $('.product-subtotal-'+productId).html(currency_format(subTotal.toFixed(2)));

            calculateProductTotal();
        }

        $('#cart-table').on('click', '.delete-cart-row', function () {
            $(this).closest('tr').remove();
            let serviceId = $(this).data('service-id');
            calculateTotal();
            if ($("#cart-table tbody tr").length == 0) {
                $("#cart-table tbody").html(`<tr id="no-service">
                            <td colspan="5" class="text-center text-danger">@lang("messages.selectService")</td>
                        </tr>`);

                $(".availableEmp").attr("data-content", "Sorry! No employee available.");
            }
            updateCoupon ();

            /* Available employee suggestion remove from pos */
            let index = cartServicesData.indexOf(serviceId);
            if (index !== -1) {
                cartServicesData.splice(index, 1);
            }
            fetch_employees();
        });

        $('#product-table').on('click', '.delete-product-row', function () {
            $(this).closest('tr').remove();
            let productId = $(this).data('product-id');
            calculateProductTotal();
            if ($("#product-table tbody tr").length == 0) {
                $("#product-table tbody").html(`<tr id="no-product">
                            <td colspan="5" class="text-center text-danger">@lang("messages.selectProduct")</td>
                        </tr>`);
            }
        });

        $('body').on('click', '#empty-cart', function() {
            swal({
                icon: "warning",
                buttons: ["@lang('app.cancel')", "@lang('app.ok')"],
                dangerMode: true,
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
            }).then((willDelete) => {
                if (willDelete) {
                    $("input[name='cart_prices[]']").each(function( index ) {
                        $(this).closest('tr').remove();
                    });
                    calculateTotal();
                    if ($("#cart-table tbody tr").length == 0) {
                        $("#cart-table tbody").html(`<tr id="no-service">
                                    <td colspan="5" class="text-center text-danger">@lang("messages.selectService")</td>
                                </tr>`);
                    }
                    updateCoupon ();

                    $("input[name='product_cart_prices[]']").each(function( index ) {
                        $(this).closest('tr').remove();
                    });
                    calculateProductTotal();
                    if ($("#product-table tbody tr").length == 0) {
                        $("#product-table tbody").html(`<tr id="no-product">
                                    <td colspan="5" class="text-center text-danger">@lang("messages.selectService")</td>
                                </tr>`);
                    }
                }
            });
        });

        $('body').on('keyup', '#cart-discount', function() {
            if ($(this).val() > 100) {
                $(this).val(100);
            }
            if(couponApplied){
                updateCoupon ();
            }else{
                calculateTotal();
            }
        });

        $('body').on('change', '#cart-tax', function() {
            calculateTotal();
            updateCoupon ();
        });

        function calculateTotal() {
            let cartTotal = 0;
            let cartSubTotal = 0;
            let cartDiscount = $('#cart-discount').val();
            let discount = 0;
            let tax = 0;

            $("input[name='cart_prices[]']").each(function( index ) {
                let servicePrice = $(this).val();
                let qty = $("input[name='cart_quantity[]']").eq(index).val();
                cartSubTotal = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));

                let taxPercent = $("input[name='tax_percent[]']").eq(index).val();
                taxAmount = ( (parseFloat(taxPercent)/100)* ((parseFloat(servicePrice) * parseInt(qty))));
                tax +=parseFloat(taxAmount);
            });

            $("#cart-sub-total").html(currency_format(cartSubTotal.toFixed(2)));

            if(parseFloat(cartDiscount) > 0){
                if(cartDiscount > 100) cartDiscount = 100;

                discount = ((parseFloat(cartDiscount)/100)*cartSubTotal);
            }

            cartSubTotal = (cartSubTotal - discount).toFixed(2);

            $('#cart-tax-amount').html(currency_format(tax.toFixed(2)));

            cartTotal = (parseFloat(cartSubTotal) + parseFloat(tax));

            if(couponAmount > 0){
                cartTotal =  (cartTotal - couponAmount);
            }

            cartTotal = cartTotal.toFixed(2)

            $("#cart-total").html(currency_format(cartTotal));
            globalCartTotal = cartTotal;

            Total();
        }

        function Total() {
            let cartTotal = 0;
            let cartSubTotal = 0;
            let cartDiscount = $('#cart-discount').val();
            let discount = 0;
            let tax = 0;
            let serviceTax = 0;
            let productTax = 0;
            let productCartSubTotal = 0;

            $("input[name='cart_prices[]']").each(function( index ) {
                let servicePrice = $(this).val();
                let qty = $("input[name='cart_quantity[]']").eq(index).val();
                cartSubTotal = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));

                let taxPercent = $("input[name='tax_percent[]']").eq(index).val();
                taxAmount = ( (parseFloat(taxPercent)/100)* ((parseFloat(servicePrice) * parseInt(qty))));
                serviceTax += parseFloat(taxAmount);
            });

            $("input[name='product_cart_prices[]']").each(function( index ) {
                let productPrice = $(this).val();
                let productQty = $("input[name='product_cart_quantity[]']").eq(index).val();
                productSubTotal = (cartSubTotal + (parseFloat(productPrice) * parseInt(productQty)));

                let taxPercentage = $("input[name='product_tax_percent[]']").eq(index).val();
                taxAmt = ((parseFloat(taxPercentage)/100) * ((parseFloat(productPrice) * parseInt(productQty))));
                productTax += parseFloat(taxAmt);
            });

            tax = serviceTax + productTax;

            $("#cart-sub-total").html(currency_format(cartSubTotal.toFixed(2)));

            if(parseFloat(cartDiscount) > 0){
                if(cartDiscount > 100) cartDiscount = 100;

                discount = ((parseFloat(cartDiscount)/100)*cartSubTotal);
            }

            cartSubTotal = (cartSubTotal - discount).toFixed(2);


            $('#cart-tax-amount').html(currency_format(tax.toFixed(2)));


            cartTotal = (parseFloat(cartSubTotal) + parseFloat(tax));

            if(couponAmount > 0)
            {
                if(cartTotal>=couponAmount )
                {
                    cartTotal =  (cartTotal - couponAmount);
                }
                else
                {
                    cartTotal = 0;
                }
            }

            cartTotal = cartTotal.toFixed(2);

            $("input[name='product_cart_prices[]']").each(function( index ) {
                let productsPrice = $(this).val();
                let q = $("input[name='product_cart_quantity[]']").eq(index).val();
                productCartSubTotal += ((parseFloat(productsPrice) * parseInt(q)));
            });

            totalCart = (parseFloat(cartTotal) + parseFloat(productCartSubTotal)).toFixed(2);

            $("#cart-total-input").val(totalCart);
            $("#total-cart").html(currency_format(totalCart));
            $("#payment-modal-total").html(currency_format(totalCart));
        }

        function calculateProductTotal() {
            let tax = 0;
            let discount = 0;
            let cartTax = '';
            let cartTotal = 0;
            let cartSubTotal = 0;
            let cartDiscount = '';

            $("input[name='product_cart_prices[]']").each(function( index ) {
                let productPrice = $(this).val();
                let qty = $("input[name='product_cart_quantity[]']").eq(index).val();
                cartSubTotal = (cartSubTotal + (parseFloat(productPrice) * parseInt(qty)));
            });

            $("#product-sub-total").html(currency_format(cartSubTotal.toFixed(2)));

            if(parseFloat(cartDiscount) > 0) {
                if(cartDiscount > 100) cartDiscount = 100;

                discount = ((parseFloat(cartDiscount)/100)*cartSubTotal);
            }

            cartSubTotal = (cartSubTotal - discount).toFixed(2);

            if(parseFloat(cartTax) > 0) {
                tax = ((parseFloat(cartTax)/100)*cartSubTotal);
                $('#cart-tax-amount').html(currency_format(tax.toFixed(2)));
            }

            cartTotal = (parseFloat(cartSubTotal) + parseFloat(tax));

            if(couponAmount > 0) {
                if(cartTotal>=couponAmount ) {
                    cartTotal =  (cartTotal - couponAmount);
                } else {
                    cartTotal = 0;
                }
            }

            cartTotal = cartTotal.toFixed(2);

            $("#product-sub-total").html(currency_format(cartTotal));

            Total();
        }

        $('body').on('click', '#do-payment', function() {
            let cartItems = $("input[name='cart_prices[]']").length;
            let userId = $("#user_id").val();
            let product_cartItems =$("input[name='product_cart_prices[]']").length;

            if(userId === null){
                swal('@lang("modules.booking.selectCustomer")');

                $('#user-error').html('@lang("modules.booking.selectCustomer")');
                return false;
            }
            else{
                $('#user-error').html('');
            }

            if(cartItems === 0 && product_cartItems === 0){
                swal('@lang("modules.booking.addItemsToCart")');
                $('#cart-item-error').html('@lang("modules.booking.addItemsToCart")');
                return false;
            }
            else{
                $('#cart-item-error').html('');
            }

            var amount = $('#total-cart').html();
            var url = "{{ route('admin.pos.show-checkout-modal',':amount') }}";
            url = url.replace(':amount', amount);
            $(modal_lg + ' ' + modal_heading).html('...');
            $.ajaxModal(modal_lg, url);
        });

        $('#payment-modal').on('shown.bs.modal', function () {
            $('#cash-given').val(globalCartTotal);
            $('#cash-return').html(currency_format(0));
            $('#cash-remaining').html(currency_format(0));
            $('#cash-given').select();
        });

        $('#cash-given').focus(function () {
            $(this).select();
        })

        $("body").on("click", "input[name='payment_gateway']", function() {
            let paymentMode = $(this).val();

            if(paymentMode === 'cash') {
                $('#cash-mode').show();
            } else {
                $('#cash-mode').hide();
            }
        });

        $('body').on('keyup', '#cash-given', function() {
            let cashGiven = $(this).val();
            if(cashGiven === ''){
                cashGiven = 0;
            }

            let total = $('#cart-total-input').val();
            let cashReturn = (parseFloat(total) - parseFloat(cashGiven)).toFixed(2);
            let cashRemaining = (parseFloat(total) - parseFloat(cashGiven)).toFixed(2);

            if(cashRemaining < 0){
                cashRemaining = parseFloat(0).toFixed(2);
            }

            if(cashReturn < 0){
                cashReturn = Math.abs(cashReturn);
            }
            else{
                cashReturn = parseFloat(0).toFixed(2);
            }

            $('#cash-return').html(currency_format(cashReturn));
            $('#cash-remaining').html(currency_format(cashRemaining));
        });

        $('body').on('click', '#submit-cart', function() {
            let location = $('#location-filter').val();

            let url = "{{route('admin.pos.store')}}";
            let bookingTime = $('#posTime').val();
            $.easyAjax({
                url: url,
                container: '#pos-form',
                type: "POST",
                data: $('#pos-form').serialize()+'&payment_gateway='+$('input[name="payment_gateway"]:checked').val()+'&pos_date='+pos_date+'&pos_time='+bookingTime+'&location='+location,
                redirect: true
            })
        });

        function checkValue(discount) {
            if(discount=='') {
                $('#cart-discount').val(0);
            }
        }

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
            return true;
        }

    </script>
    @include("partials.currency_format")
@endpush
