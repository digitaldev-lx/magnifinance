<style>
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
    .select2-container {
	    width: 100% !important;
    }
</style>
@if ($current_url == 'calendarPage')
    <div class="modal-header">
        <h4 class="modal-title">@lang('app.editBooking')</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    </div>
@endif
<div class="modal-body">
    <form action="" id="update-form" class="ajax-form">
        @method('PUT')
        @csrf

        <div class="row mt-2 mb-3">
            <div class="col-md-4 border-right"> <strong>@lang('app.name')</strong> <br>
                <p class="text-muted"><i class="icon-user"></i> {{ ucwords($booking->user->name) }}</p>
            </div>
            <div class="col-md-4 border-right"> <strong>@lang('app.email')</strong> <br>
                <p class="text-muted"><i class="icon-email"></i> {{ $booking->user->email ?? '--' }}</p>
            </div>
            <div class="col-md-4"> <strong>@lang('app.mobile')</strong> <br>
                <p class="text-muted"><i class="icon-mobile"></i> {{ $booking->user->mobile ? $booking->user->formatted_mobile : '--' }}</p>
            </div>
        </div>
        <hr>

        @if ($booking->deal_id!='')
            <div class="row">
                <div class="col-md-12 border-right"> <strong>@lang('app.deal') @lang('app.name')</strong> <br>
                    <a data-toggle="tooltip" data-original-title="@lang('app.view') @lang('app.deal')" href="{{ route('admin.deals.index') }}">{{$booking->deal->title}}</a>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6 border-right"> <strong>@lang('app.deal') @lang('app.location')</strong> <br>
                    <p> {{$booking->deal->location->name}} </p>
                </div>
                <div class="col-md-6 border-right"> <strong>@lang('app.deal') @lang('app.quantity')</strong> <br>
                    <p> {{$booking->deal_quantity}} </p>
                </div>
            </div>
            <hr>
        @endif

        <div class="row">
            <div class="col-sm-4"> <strong>@lang('app.booking') @lang('app.date')</strong> <br>
                <div class="form-group">
                    <input type="text" class="form-control datepicker" name="boking_date" value="
                    @if ($booking->date_time!='') {{ $booking->date_time->format($settings->date_format) }} @endif ">
                    <input type="hidden" name="booking_date" id="booking_date" value="@if ($booking->date_time!='') {{ $booking->date_time->format('Y-m-d') }} @endif">


                </div>
            </div>
            <div class="col-sm-4"> <strong>@lang('app.booking') @lang('app.time') </strong> <br>
                <div class="form-group">
                    <div class="input-group date">
                        <input type="text" class="form-control" name="booking_time" id="booking_time" value=" @if ($booking->date_time!='') {{ $booking->date_time->format($settings->time_format) }} @endif  ">
                        <span class="input-group-append input-group-addon">
                            <button type="button" class="btn btn-default">
                                <span class="fa fa-clock-o"></span>
                            </button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4"> <strong>@lang('app.booking') @lang('app.status')</strong> <br>
                <div class="form-group">
                    <select name="status" id="booking-status" class="form-control">
                        <option value="completed" @if($booking->status == 'completed') selected @endif>@lang('app.completed')</option>
                        <option value="pending" @if($booking->status == 'pending') selected @endif>@lang('app.pending')</option>
                        <option value="approved" @if($booking->status == 'approved') selected @endif>@lang('app.approved')</option>
                        <option value="in progress" @if($booking->status == 'in progress') selected @endif>@lang('app.in progress')</option>
                        <option value="canceled" @if($booking->status == 'canceled') selected @endif>@lang('app.canceled')</option>
                    </select>
                </div>
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-sm-12"> <strong>@lang('menu.employee')</strong> <br>
                <div class="form-group">
                    <select name="employee_id[]" id="employee_id" class="form-control" multiple="multiple">
                        <option value=""> @lang('app.selectEmployee') </option>
                        @foreach($employees as $employee)
                            <option
                                    @if(in_array($employee->id, $selected_booking_user)) selected @endif
                            value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <hr>

        <div class="row">

                <div class="col-md-6 mb-2 text-left">
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-plus"></i> @lang('app.add') @lang('app.service')
                        </button>
                        <div class="dropdown-menu">
                            @foreach($businessServices as $service)
                                <a class="dropdown-item add-item"
                                data-price="{{ $service->discounted_price }}"
                                data-service-id="{{ $service->id }}"
                                data-total_tax_percent="{{ $service->total_tax_percent }}"
                                data-type-id="service"
                                href="javascript:;">{{ ucwords($service->name) }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2 text-right">
                    <div class="dropdown">
                        <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-plus"></i> @lang('app.add') @lang('app.product')
                            </button>
                        <div class="dropdown-menu">
                            @foreach($products as $product)
                                <a class="dropdown-item add-product"
                                    data-price="{{ $product->discounted_price }}"
                                    data-product-id="{{ $product->id }}"
                                    data-total_tax_percent="{{ $product->total_tax_percent }}"
                                    data-type-id="product"
                                    href="javascript:;">{{ ucwords($product->name) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

            <div class="col-md-12">
                <table class="table table-condensed" id="cart-table">
                    <thead class="bg-secondary">
                    <tr>
                        <th>@lang('app.item')</th>
                        <th>@lang('app.unitPrice')</th>
                        <th width="120">@lang('app.quantity')</th>
                        <th class="text-right">@lang('app.amount')</th>
                        @if ($booking->deal_id=='')
                            <th><i class="icon-settings"></i></th>
                        @endif

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($booking->items as $key=>$item)


                        @php
                            $item_name = '';
                            $type = '';
                            $item_id = '';
                            if(!is_null($item->deal_id) && is_null($item->business_service_id) && is_null($item->product_id)) {
                                $item_name = ucwords($item->deal->title);
                                $type = 'deal';
                                $item_id = $item->deal_id;
                            }
                            else if(!is_null($item->business_service_id) && is_null($item->deal_id) && is_null($item->product_id)) {
                                $type = 'service';
                                $item_name = ucwords($item->businessService->name);
                                $item_id = $item->business_service_id;
                            }
                        @endphp

                        @php
                            $appliedTax = 0;
                            $taxPercent = 0;
                            $subTotal = 0;
                            if($type == 'service') {
                                $subTotal = $item->quantity * $item->businessService->net_price;
                            }
                            else if ($type == 'deal') {
                                $subTotal = $item->quantity * $item->deal->deal_amount;
                            }

                            if (!is_null($item) && $type == 'service') {
                                $taxPercent += $item->businessService->total_tax_percent;
                                $appliedTax += ($subTotal*$item->businessService->total_tax_percent)/100;
                            }else if (!is_null($item) && $type == 'deal') {
                                $taxPercent += $item->deal->total_tax_percent;
                                $appliedTax += ($subTotal*$item->deal->total_tax_percent)/100;
                            }

                        @endphp

                        @if (is_null($item->product_id))
                        <tr>
                            <td>
                                <input type="hidden" name="types[]" value="{{ $type }}">
                                <input type="hidden" name="item_ids[]" value="{{ $item_id }}">
                                {{ $item_name }}
                            </td>
                            <td>
                                <input type="hidden" name="item_prices[]" value="{{ number_format((float)$item->unit_price, 2, '.', '') }}" class="cart-price-{{$item->id}}">

                                <input type="hidden" name="tax_percent[]" class="tax_percent" value="{{ $taxPercent }}">
                                <input type="hidden" name="tax_amount[]" class="tax_amount" value="{{ $appliedTax }}">

                                {{ currencyFormatter(number_format((float)$item->unit_price, 2, '.', ''),myCurrencySymbol()) }}
                            </td>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <button type="button" class="btn btn-default quantity-minus" data-service-id="{{ $item->id}}"><i class="fa fa-minus"></i></button>
                                    </div>
                                    <input type="text" readonly name="cart_quantity[]" data-service-id="{{ $item->id }}" class="form-control cart-service-{{ $item->id }}" value="{{ $item->quantity }}">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-default quantity-plus" id="btn{{$item->id}}" data-service-id="{{ $item->id }}"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </td>

                            <td class="text-right cart-subtotal-{{ $item->id }}">
                                {{ currencyFormatter(number_format((float)($item->unit_price  * $item->quantity), 2, '.', ''),myCurrencySymbol()) }}
                            </td>
                            <td>
                                <a href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-cart-row"><i class="fa fa-times" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>

                </table>

                {{-- PRODUCT TABLE --}}
                <table class="table table-condensed" id="product-table">
                    <thead class="bg-secondary">
                    <tr>
                        <th>@lang('app.product')</th>
                        <th>@lang('app.unitPrice')</th>
                        <th width="120">@lang('app.quantity')</th>
                        <th class="text-right">@lang('app.amount')</th>
                        @if ($booking->deal_id=='')
                            <th><i class="icon-settings"></i></th>
                        @endif

                    </tr>
                    </thead>
                    <tbody>
                    @forelse($booking->items as $key=>$item)
                        @if (is_null($item->business_service_id) && is_null($item->deal_id))
                            @php
                                $appliedTax = 0;
                                $taxPercent = 0;
                                $subTotal = 0;
                                $subTotal = $item->quantity * $item->product->discounted_price;
                                $taxPercent += $item->product->total_tax_percent;
                                $appliedTax += ($subTotal*$item->product->total_tax_percent)/100;
                            @endphp
                            <tr>
                                <td><input type="hidden" name="cart_products[]" value="{{ $item->product->id }}">
                                    {{$item->product->name}}
                                </td>
                                <td>
                                    <input type="hidden" name="product_prices[]" class="product-price-{{ $item->product->id }}" value="{{ number_format((float)$item->unit_price, 2, '.', '') }}">{{ $settings->currency->currency_symbol.number_format((float)$item->unit_price, 2, '.', '') }}
                                    <input type="hidden" name="product_percent[]" class="product_percent" value="{{ $taxPercent }}">
                                    <input type="hidden" name="product_amount[]" class="product_amount" value="{{ $appliedTax }}">
                                </td>
                                <td>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button type="button" class="btn btn-default product-quantity-minus" data-product-id="{{ $item->product->id }}"><i class="fa fa-minus"></i></button>
                                        </div>
                                        <input type="text" readonly name="product_quantity[]" data-product-id="{{ $item->product_id }}" class="form-control cart-product-{{ $item->product->id }}" value="{{ $item->quantity }}">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-default product-quantity-plus" id="productBtn{{$item->product->id}}" data-product-id="{{ $item->product->id }}"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-right product-subtotal-{{ $item->product->id }}">{{ $settings->currency->currency_symbol.number_format((float)($item->product->discounted_price  * $item->quantity), 2, '.', '') }}</td>
                                <td>
                                    <a href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-product-row"><i class="fa fa-times" aria-hidden="true"></i></a>
                                </td>

                            </tr>
                        @endif
                    @empty
                        <tr id="no-product">
                            <td colspan="5" class="text-center text-danger">@lang("messages.selectProduct")</td>
                        </tr>
                    @endforelse
                    </tbody>

                </table>
            </div>


            <div class="col-md-6 border-top">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-condensed">
                            <tr class="h6">
                                <td class="border-top-0">@lang('modules.booking.paymentMethod')</td>
                                <td class="border-top-0 "><i class="fa fa-money"></i> {{ $booking->payment_gateway }}</td>
                            </tr>
                            <tr class="h6">
                                <td>@lang('modules.booking.paymentStatus')</td>
                                <td>
                                    <div class="form-group">
                                        <select name="payment_status" id="payment-status" class="form-control select2">
                                            <option value="completed" @if($booking->payment_status == 'completed') selected @endif>@lang('app.completed')</option>
                                            <option value="pending" @if($booking->payment_status == 'pending') selected @endif>@lang('app.pending')</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 border-top">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-condensed">
                            <tr class="h6">
                                <td class="border-top-0 text-right w-50">@if($type == 'deal') @lang('app.deal') @else @lang('app.service') @endif @lang('app.total')</td>
                                <td class="border-top-0" id="cart-sub-total">{{ currencyFormatter(number_format((float)$booking->original_amount, 2, '.', ''),myCurrencySymbol()) }}</td>
                            </tr>
                            
                            <tr class="h6">
                                <td class="text-right">@lang('app.product') @lang('app.total')</td>
                                <td id="product-total">{{ currencyFormatter(number_format((float)($booking->product_amount), 2, '.', ''),myCurrencySymbol()) }}
                                    <input type="hidden"  id="product-total-input">
                                </td>
                            </tr>

                            @if($booking->deal_id == '')
                                <tr class="h6">
                                    <td class="text-right">@lang('app.discountOnService')</td>
                                    <td><input type="number" id="cart-discount" name="cart_discount" class="form-control" step=".01" min="0" value="{{ $booking->discount_percent }}"></td>
                                </tr>
                            @endif
                        
                            @if($booking->coupon_discount > 0)
                                <tr class="h6">
                                    <input type="hidden" id="coupon_id" name="coupon_id" value="{{ $booking->coupon_id }}">
                                    <input type="hidden" id="coupon_amount" name="coupon_amount" value="{{ $booking->coupon_discount }}">
                                    <td class="text-right">@lang('app.couponDiscount') ({{ $booking->coupon->title}})</td>
                                    <td id="couponAmount">{{ currencyFormatter(number_format((float)$booking->coupon_discount, 2, '.', ''),myCurrencySymbol()) }}</td>
                                </tr>
                            @endif

                            @if($booking->tax_amount > 0)
                                <tr class="h6">
                                    <input type="hidden" id="cart-tax" name="cart_tax" value="{{ $booking->tax_percent }}">
                                    <td class="text-right">@lang('app.totalTax')</td>
                                    <td id="cart-tax-amount">{{ currencyFormatter(number_format((float)$booking->tax_amount, 2, '.', ''),myCurrencySymbol()) }}</td>
                                </tr>
                            @endif

                            <tr class="h5">
                                <td class="text-right">@lang('app.total')</td>
                                <td id="total-cart">{{ currencyFormatter(number_format((float)$booking->amount_to_pay, 2, '.', ''),myCurrencySymbol()) }}
                                    <input type="hidden"  id="cart-total-input">
                                </td>
                            </tr>
                        </table>
                        <!-- to redirect after payment -->
                        <input type="hidden" id="current_url" name="current_url" value="bookingPage">
                    </div>
                </div>

            </div>
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="mt-2">
                        <div id="cart-item-error" class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $booking_date = $booking->date_time!='' ? $booking->date_time->format($settings->time_format) : '';
        @endphp

        <input type="hidden" name="hidden_booking_time" id="hidden_booking_time" value="{{ $booking_date  }}">
    </form>
</div>
<div class="modal-footer">
    <button class="btn btn-outline-danger delete-row" data-row-id="{{ $booking->id }}" type="button"><i class="fa fa-times"></i> @lang('app.delete') @lang('app.booking')</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times"></i>
        @lang('app.cancel')</button>
    <button type="button" id="update-booking" data-booking-id="{{ $booking->id }}" class="btn btn-success"><i class="fa fa-check"></i> @lang('app.submit')</button>
</div>

<script src="{{ asset('js/utils.js') }}"></script>
<script>
    var couponAmount = 0;
    var couponCodeValue = '';

    $("#employee_id").select2({
        placeholder: "Select Employee",
        allowClear: true
    });

    $('.datepicker').datetimepicker({
        format: '{{ $date_picker_format }}',
        locale: '{{ $settings->locale }}',
        allowInputToggle: true,
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down",
            previous: "fa fa-angle-double-left",
            next: "fa fa-angle-double-right"
        }
    }).on('dp.change', function(e) {
        $('#booking_date').val(moment(e.date).format('YYYY-MM-DD'));
    });

    $('#booking_time').datetimepicker({
            format: '{{ $time_picker_format }}',
            locale: '{{ $settings->locale }}',
            allowInputToggle: true,
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                previous: "fa fa-angle-double-left",
                next: "fa fa-angle-double-right",
            },
        }).on('dp.change', function(e) {
            $('#hidden_booking_time').val(convert(e.date));
    });

    $("#cart-table").on('change', "input[name='cart_quantity[]']", function () {
        let serviceId = $(this).data('service-id');
        let qty = $(this).val();

        updateCartQuantity(serviceId, qty);
    });

    $("#product-table").on('change', "input[name='product_quantity[]']", function () {
        let productId = $(this).data('product-id');
        let qty = $(this).val();

        updateProductQuantity(productId, qty);
    });

    $('#cart-table').on('click', '.quantity-minus', function () {
        if('{{$booking->deal_id}}'!=''){
            return false;
        }
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

        // if('{{$booking->deal_id}}'!=''){
        //     return false;
        // }

        let serviceId = $(this).data('service-id');

        // console.log(serviceId);

        let qty = $('.cart-service-'+serviceId).val();
        qty = parseInt(qty)+1;

        $('.cart-service-'+serviceId).val(qty);

        updateCartQuantity(serviceId, qty);
    });

    $('#product-table').on('click', '.product-quantity-minus', function () {
        if('{{$booking->deal_id}}'!=''){
            return false;
        }
        let productId = $(this).data('product-id');

        let qty = $('.cart-product-'+productId).val();
        qty = parseInt(qty)-1;

        if(qty < 1){
            qty = 1;
        }
        $('.cart-product-'+productId).val(qty);

        updateProductQuantity(productId, qty);
    });

    $('#product-table').on('click', '.product-quantity-plus', function () {

        if('{{$booking->deal_id}}'!=''){
            return false;
        }

        let productId = $(this).data('product-id');

        let qty = $('.cart-product-'+productId).val();
        qty = parseInt(qty)+1;

        $('.cart-product-'+productId).val(qty);

        updateProductQuantity(productId, qty);
    });

    function updateCartQuantity(serviceId, qty) {
        let servicePrice = $('.cart-price-'+serviceId).val();

        let subTotal = (parseFloat(servicePrice) * parseInt(qty));

        $('.cart-subtotal-'+serviceId).html(currency_format(subTotal.toFixed(2)));

        calculateTotal();
        updateCoupon ();
    }

    function updateProductQuantity(productId, qty) {

        let productPrice = $('.product-price-'+productId).val();

        let subTotal = (parseFloat(productPrice) * parseInt(qty));

        $('.product-subtotal-'+productId).html(currency_format(subTotal.toFixed(2)));

        calculateProductTotal();
    }


    $('#cart-table').on('click', '.delete-cart-row', function () {
        $(this).closest('tr').remove();
        calculateTotal();
        updateCoupon ();
    });

    $('#product-table').on('click', '.delete-product-row', function () {
        $(this).closest('tr').remove();
        calculateProductTotal();
    });

    $('#cart-discount').keyup(function () {
        if ($(this).val() == '') {
            $(this).val(0);
        }
        if ($(this).val() > 100) {
            $(this).val(100);
        }
        calculateTotal();
        updateCoupon ();
    });

    $('#cart-tax').change(function () {
        calculateTotal();
        updateCoupon ();
    });

    function calculateTotal() {
        let cartTotal = 0;
        let cartSubTotal = 0;
        let cartDiscount = $('#cart-discount').val();
        let cartTax = $('#cart-tax').val();
        let discount = 0;
        let tax = 0;

        $("input[name='item_prices[]']").each(function( index ) {
            let servicePrice = $(this).val();
            let qty = $("input[name='cart_quantity[]']").eq(index).val();
            cartSubTotal = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));

            let taxPercent = $("input[name='tax_percent[]']").eq(index).val();
            taxAmount = ((parseFloat(taxPercent)/100)* ((parseFloat(servicePrice) * parseInt(qty))));
            tax += parseFloat(taxAmount);
        });

        $("#cart-sub-total").html(currency_format(cartSubTotal.toFixed(2)));

        if(parseFloat(cartDiscount) > 0){
            if(cartDiscount > 100) cartDiscount = 100;

            discount = ((parseFloat(cartDiscount)/100)*cartSubTotal);

        }
        cartSubTotal = (parseFloat(cartSubTotal) - discount).toFixed(2);

        $('#cart-tax-amount').html(currency_format(tax.toFixed(2)));

        cartTotal = (parseFloat(cartSubTotal) + tax);

        couponAmount = $('#coupon_amount').val();
        if(couponAmount)
        {
            if(cartTotal>couponAmount)
            {
                cartTotal =  (cartTotal - couponAmount);
            }
            else
            {
                cartTotal = 0;
            }
        }

        cartTotal =  parseFloat(cartTotal).toFixed(2);

        $("#cart-total").html(currency_format(cartTotal));

        total();
    }

    function calculateProductTotal() {

        let cartTotal = 0;
        let cartSubTotal = 0;
        let cartDiscount = $('#product-discount').val();
        let cartTax = $('#cart-tax').val();
        let discount = 0;
        let tax = 0;

        $("input[name='product_prices[]']").each(function( index ) {
            let productPrice = $(this).val();
            let qty = $("input[name='product_quantity[]']").eq(index).val();
            cartSubTotal = (cartSubTotal + (parseFloat(productPrice) * parseInt(qty)));
        });

        $("#product-total").html(currency_format(cartSubTotal.toFixed(2)));

        total();
    }

    function total() {
        let cartTotal = 0;
        let cartSubTotal = 0;
        let cartDiscount = $('#cart-discount').val();
        let cartTax = $('#cart-tax').val();
        let discount = 0;
        let tax = 0;
        let serviceTax = 0;
        let productTax = 0;
        let productSubTotal = 0;

        $("input[name='item_prices[]']").each(function( index ) {
            let servicePrice = $(this).val();
            let qty = $("input[name='cart_quantity[]']").eq(index).val();
            cartSubTotal = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));

            let taxPercent = $("input[name='tax_percent[]']").eq(index).val();
            taxAmount = ( (parseFloat(taxPercent)/100)* ((parseFloat(servicePrice) * parseInt(qty))));
            serviceTax += parseFloat(taxAmount);
        });

        $("input[name='product_prices[]']").each(function( index ) {
                let productPrice = $(this).val();
                let productQty = $("input[name='product_quantity[]']").eq(index).val();
                productSubTotal += (parseFloat(productPrice) * parseInt(productQty));

                let taxPercentage = $("input[name='product_percent[]']").eq(index).val();
                console.log(taxPercentage);
                taxAmt = ((parseFloat(taxPercentage)/100) * ((parseFloat(productPrice) * parseInt(productQty))));
                productTax += parseFloat(taxAmt);
        });

        tax = serviceTax + productTax;

        $("#cart-sub-total").html(currency_format(cartSubTotal.toFixed(2)));

        $("#product-total").html(currency_format(productSubTotal.toFixed(2)));

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

        totalCart = (parseFloat(cartTotal) + parseFloat(productSubTotal)).toFixed(2);

        $("#cart-total-input").val(totalCart);
        $("#total-cart").html(currency_format(totalCart));
        $("#payment-modal-total").html(currency_format(totalCart));
    }

    $('body').on('click', '.add-item', function(){
        let serviceId = $(this).data('service-id');
        let type = $(this).data('type');
        let serviceName = $(this).html();
        let taxPercent = $(this).data('total_tax_percent');

        let servicePrice = parseFloat($(this).data('price')).toFixed(2);
        let serviceItems = $('#cart-table tbody tr td:first-child input[type="hidden"]');
        let serviceItemsCount = 0;

        let item = '<tr>\n' +
            '                    <td><input type="hidden" name="types[]" value="service"><input type="hidden" name="item_ids[]" value="'+serviceId+'">\n' +
            '                        '+serviceName+'</td>\n' +
            '                    <td><input type="hidden" class="cart-price-'+serviceId+'" name="item_prices[]" value="'+servicePrice+'"><input type="hidden" name="tax_percent[]" value="'+taxPercent+'">'+currency_format(servicePrice)+'</td>\n' +
            '                    <td>\n' +
            '                        <div class="input-group">\n' +
            '                            <div class="input-group-prepend">\n' +
            '                                <button type="button" class="btn btn-default quantity-minus" data-service-id="'+serviceId+'"><i class="fa fa-minus"></i></button>\n' +
            '                            </div>\n' +
            '                            <input type="text" readonly name="cart_quantity[]" data-service-id="'+serviceId+'" class="form-control cart-service-'+serviceId+'" value="1">\n' +
            '                            <div class="input-group-append">\n' +
            '                                <button type="button" class="btn btn-default quantity-plus" id="btn'+serviceId+'" data-service-id="'+serviceId+'"><i class="fa fa-plus"></i></button>\n' +
            '                            </div>\n' +
            '                        </div>\n' +
            '                    </td>\n' +
            '                    <td class="text-right cart-subtotal-'+serviceId+'">'+currency_format(servicePrice)+'</td>\n' +
            '                    <td>\n' +
            '                        <a href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-cart-row"><i class="fa fa-times" aria-hidden="true"></i></a>\n' +
            '                    </td>\n' +
            '                </tr>';


        serviceItems.each(function()
        {
            if(this.value==serviceId)
            {
                serviceItemsCount += 1;
            }
        });

        if(serviceItemsCount>0)
        {
            $('#btn'+serviceId).click();
        }
        else
        {
            $('#cart-table tbody').append(item);
        }

        calculateTotal();
        updateCoupon ();
    });

    $('body').on('click', '.add-product', function(){
        let productId = $(this).data('product-id');
        let productName = $(this).html();
        let productTaxPercent = $(this).data('total_tax_percent');
        let productPrice = parseFloat($(this).data('price')).toFixed(2);
        let productItems = $('#product-table tbody tr td:first-child input[type="hidden"]');
        let productItemsCount = 0;

        let item = '<tr>\n' +
            '                    <td><input type="hidden" name="cart_products[]" value="'+productId+'">\n' +
            '                        '+productName+'</td>\n' +
            '                    <td><input type="hidden" name="product_prices[]" class="product-price-'+productId+'" value="'+productPrice+'"><input type="hidden" name="product_percent[]" value="'+productTaxPercent+'">'+currency_format(productPrice)+'</td>\n' +
            '                    <td>\n' +
            '                        <div class="input-group">\n' +
            '                            <div class="input-group-prepend">\n' +
            '                                <button type="button" class="btn btn-default product-quantity-minus" data-product-id="'+productId+'"><i class="fa fa-minus"></i></button>\n' +
            '                            </div>\n' +
            '                            <input type="text" readonly name="product_quantity[]" data-product-id="'+productId+'" class="form-control cart-product-'+productId+'" value="1">\n' +
            '                            <div class="input-group-append">\n' +
            '                                <button type="button" class="btn btn-default product-quantity-plus" id="btn'+productId+'" data-product-id="'+productId+'"><i class="fa fa-plus"></i></button>\n' +
            '                            </div>\n' +
            '                        </div>\n' +
            '                    </td>\n' +
            '                    <td class="text-right product-subtotal-'+productId+'">'+currency_format(productPrice)+'</td>\n' +
            '                    <td>\n' +
            '                        <a href="javascript:;" class="btn btn-danger btn-sm btn-circle delete-product-row"><i class="fa fa-times" aria-hidden="true"></i></a>\n' +
            '                    </td>\n' +
            '                </tr>';


        productItems.each(function()
        {
            if(this.value==productId)
            {
                productItemsCount += 1;
            }
        });

        if(productItemsCount>0)
        {
            $('#productBtn'+productId).click();
        }
        else
        {
            $('#product-table tbody').append(item);
        }

        calculateProductTotal();

    });

    // Update coupon during change discount
    function updateCoupon () {

        let cartTotal = 0;
        let cartSubTotal = 0;
        let cartDiscount = $('#cart-discount').val();
        let cartTax = $('#cart-tax').val();
        let discount = 0;
        let tax = 0;

        $("input[name='item_prices[]']").each(function( index ) {
            let servicePrice = $(this).val();
            let qty = $("input[name='cart_quantity[]']").eq(index).val();
            cartSubTotal = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));

            let taxPercent = $("input[name='tax_percent[]']").eq(index).val();
            taxAmount = ( (parseFloat(taxPercent)/100)* ((parseFloat(servicePrice) * parseInt(qty))));
            tax += parseFloat(taxAmount);
        });

        $("#cart-sub-total").html(currency_format(cartSubTotal.toFixed(2)));

        if(parseFloat(cartDiscount) > 0){
            if(cartDiscount > 100) cartDiscount = 100;

            discount = ((parseFloat(cartDiscount)/100)*cartSubTotal);

        }
        cartSubTotal = (parseFloat(cartSubTotal) - discount).toFixed(2);

        $('#cart-tax-amount').html(currency_format(tax.toFixed(2)));

        cartTotal = (parseFloat(cartSubTotal) + tax).toFixed(2);


        @if($booking->coupon_id)

            cartSubTotal   = 0;
            var cart_discount  = $('#cart-discount').val();
            var cartServices   = [];
            var coupon_id      = {{$booking->coupon_id}};

            $("input[name='cart_prices[]']").each(function( index ) {
                let servicePrice = $(this).val();
                let qty = $("input[name='cart_quantity[]']").eq(index).val();
                cartServices = (cartSubTotal + (parseFloat(servicePrice) * parseInt(qty)));
            });

            if(cartServices === undefined || cartServices === "" || cartServices === null ||
                cartServices.length <= 0){
                return false;
            }

            var token = '{{ csrf_token() }}';

            $.easyAjax({
                url: '{{ route('admin.bookings.update-coupon') }}',
                type: 'POST',
                data: {'_token':token,'coupon_id':coupon_id, 'cart_discount': cart_discount, 'cart_services': cartServices},
                success: function (response) {
                    if(response.status != 'fail'){
                        couponAmount = response.amount;
                        if(couponAmount>cartTotal)
                        {
                            couponAmount = cartTotal;
                        }
                        $('#couponAmount').html(currency_format(couponAmount));
                        $('#coupon_amount').val(couponAmount);
                        calculateTotal();
                    }
                }
            });

        @endif
    }

    function convert(str) {
        var date = new Date(str);
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        hours = ("0" + hours).slice(-2);
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }
</script>

@include("partials.currency_format")
