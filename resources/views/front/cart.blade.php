@extends('layouts.front')

@push('styles')
    <link href="{{ asset('front/css/cart.css') }}" rel="stylesheet">
    <style>
        .d-none {
            display: none;
        }
        .CouponBox {
            border-radius: 4px;
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <!-- CART START -->
    <section class="booking_step_section">
        <div class="container">
            <div class="row ">
                <div class="col-12">
                    <div class="booking_step_heading text-center">
                        <h1 class="text-left">@lang('front.headings.bookingDetails')</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-12 mb-30">
                    <div class="shopping-cart-table">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th>@lang('front.table.headings.serviceName')</th>
                                    <th>@lang('front.table.headings.unitPrice')</th>
                                    <th>@lang('front.table.headings.quantity')</th>
                                    @if(!is_null($taxes))
                                    <th>@lang('app.tax')</th>
                                    @endif
                                    <th>@lang('front.table.headings.subTotal')</th>
                                    @if (!is_null($products))
                                        <th>&nbsp;</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if (!is_null($products))
                                    @foreach($products as $key => $product)
{{--                                        @dd($product)--}}
                                        <tr id="{{ $key }}">
                                            <td>{{ $product['name'] }}
                                            </td>
                                            <td class="rupee">{{ currencyFormatter($product['price']) }}</td>

                                            {{--@if($product['tax_on_price_status'] == 'active')
                                                <?php
                                                $unit_price = round($product['price'] / (1 + json_decode($product['tax'])[0]->percent / 100), 2);
                                                $tax_amount = round($product['price'] - $unit_price, 2);
                                                ?>
                                                <td class="rupee">{{ currencyFormatter($unit_price) }}</td>
                                            @else
                                                <td class="rupee">{{ currencyFormatter($product['price']) }}</td>
                                            @endif--}}
                                            <td>
                                                <div></div>
                                                <div class="qty-wrap">
                                                    <div class="value-button qty-elements decrease" value="Decrease Value"><i class="zmdi zmdi-minus"></i></div>

                                                    <input
                                                    type="number"
                                                    id="number"
                                                    name="qty"
                                                    onkeypress="return isNumberKey(event)"
                                                    value="{{ $product['quantity'] }}"
                                                    class="input-text qty"
                                                    data-id="{{ $product['unique_id'] }}"
                                                    data-deal-id="{{ $product['id'] }}"
                                                    data-price="{{$product['price']}}"
                                                    data-tax-on-price-status="{{$product['tax_on_price_status']}}"
                                                    data-type="{{$product['type']}}"
                                                    @if ($product['type'] == 'deal')
                                                        data-max-order="{{$product['max_order']}}"
                                                    @endif
                                                    autocomplete="none">

                                                    <div class="value-button qty-elements increase" value="Increase Value"><i class="zmdi zmdi-plus"></i></div>
                                                </div>
                                            </td>
                                            @if(!is_null($taxes))
                                                <td class="tax_detail" data-tax-on-price-status="{{$product['tax_on_price_status']}}" style="width: 15%">
                                                    @php
                                                        $totalTax = 0;
                                                        $appliedTax = 0;
                                                        $taxPercent = 0;
                                                        $subTotal = $product['quantity'] * $product['price'];
                                                    @endphp

                                                    @if (isset($product['tax']))
                                                        @forelse (json_decode($product['tax']) as $tax)
                                                            @if (isset($tax->percent))
                                                                @php
                                                                    $taxPercent += $tax->percent;
                                                                    $appliedTax += ($subTotal*($tax->percent/100));


                                                                @endphp
                                                                {{-- todo: corrigir valores e imposto--}}
                                                                {{ $tax->name }}-<span>{{ $tax->percent }}% @if(!$loop->last),@endif</span>
                                                            @endif
                                                        @empty
                                                            <span>--</span>
                                                        @endforelse
                                                    @endif

                                                    <input type="hidden" class="tax_percent" value="{{ $taxPercent }}">
                                                    <input type="hidden" class="tax_amount" value="{{ $appliedTax }}">
                                                </td>
                                            @endif
                                            <td class="sub-total rupee">
                                                    <input type="hidden" value="{{ $product['quantity'] * $product['price'] }}">
                                                    <span>{{ currencyFormatter($product['quantity'] * $product['price']) }}</span>
                                            </td>
                                            <td>
                                                <a title="@lang('front.table.deleteProduct')" href="javascript:;" data-key="{{ $key }}" class="delete-item delete-btn">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>

                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-danger">@lang('front.table.emptyMessage')</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6">
                                        <ul class="cart-buttons">
                                            <li>
                                            </li>
                                            <li>
                                                <a href="javascript:;" data-key="all" style="width: unset; !important;" class="btn btn-custom btn-blue delete-item" id="clear-cart">@lang('front.buttons.clearCart')</a>
                                                <a href="{{ route('front.index') }}" class="btn btn-custom btn-blue">@lang('front.buttons.continueBooking')</a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4 col-12 mb-30">

                    <div class="cart-block mt-3 mt-lg-0">
                        <div class="final-cart">
                            <h5>@lang('front.summary.cart.heading.cartTotal')</h5>

                            @if ($type == 'booking')
                                <div class="mx-3 mt-4 @if(is_null($couponData)) CouponBox @else d-none @endif" id="applyCouponBox">
                                    <div class="input-group">
                                        <input type="text" name="coupon" class="form-control" placeholder="@lang('front.summary.cart.applyCoupon')" id="coupon">
                                        <div class="input-group-prepend">
                                            <button id="apply-coupon" type="button" class="mt-2 mt-lg-0 mt-md-0 btn btn-sm input-group-text">@lang('front.summary.cart.applyCoupon')</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="pb-0 cart-value @if(is_null($couponData)) d-none @endif" id="removeCouponBox">
                                    <h6  class="clearfix">@lang('app.coupons')</h6>
                                    <div class="coupons-base-content justify-content-between d-flex align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <span class="coupons-name mb-0 text-uppercase" id="couponCode" >
                                                    @if(!is_null($couponData))
                                                        {{ $couponData[0]['code'] }}
                                                    @endif
                                                </span>
                                                <small class="mb-0 text-success savetext d-block">
                                                    @lang('app.youSaved')
                                                    <span id="couponCodeAmonut" class="rupee">
                                                        @if(!is_null($couponData))
                                                            {{ currencyFormatter($couponData['applyAmount']) }}
                                                        @endif
                                                    </span>
                                                </small>
                                            </div>
                                        </div>
                                        <div>
                                            <button id="remove-coupon" type="button" class="btn btn-sm btn-danger remove-button"> @lang('app.remove') </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="cart-value">
                                <ul class="cart-value-detail">

                                    @if($type == 'booking')
                                        <li>
                                            <span>
                                                @lang('front.summary.cart.subTotal')
                                            </span>
                                            <span id="sub-total" class="rupee">
                                            </span>
                                        </li>
                                    @endif

                                    @if($type == 'booking')

                                        @if(!is_null($products))
                                            <li class="couponDiscountBox">
                                                <span>
                                                    @lang('app.totalTax')
                                                </span>
                                                <span id="tax" class="rupee">
                                                </span>
                                            </li>
                                        @endif
                                    @endif

                                    @if($type == 'booking')
                                        @if(!is_null($couponData))
                                            <li class="couponDiscountBox" id="couponDiscountBox">
                                                <span class="text-uppercase">
                                                    @lang('app.discount') ({{ $couponData[0]['code'] }}):
                                                </span>
                                                <span id="couponDiscoiunt">
                                                    -{{ currencyFormatter($couponData['applyAmount']) }}
                                                </span>
                                            </li>
                                        @endif
                                    @endif


                                </ul>
                                <ul class="cart-total-amount">
                                    <li id="totalAmountBox" class="mb-0">
                                        <span>
                                            @lang('front.summary.cart.totalAmount'):
                                        </span>
                                        <span id="total" class="rupee">
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (!is_null($products))
                <div class="row">
                    <div class="col-12 d-flex justify-content-between booking_step_buttons">
                        <button class="btn d-flex align-items-center go-back"><i class="zmdi zmdi-long-arrow-left"></i>@lang('front.navigation.goBack')
                        </button>
                        <button class="btn d-flex align-items-center next-step">
                            {{ (!is_null($type) && $type == 'deal') ? __('front.navigation.toCheckout') : __('front.selectBookingTime') }}
                            <i class="zmdi zmdi-long-arrow-right"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </section>
    <!-- CART END -->
@endsection

@push('footer-script')
    <script src="{{ asset('assets/js/cookie.js') }}"></script>
    <script>

        function isNumberKey(evt){
            var charCode = (evt.which) ? evt.which : evt.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
            return true;
        }

        $(function () {
            var couponCode = '';
            calculateTotal();
        });

        var cartUpdate;
        var couponAmount = 0;
        var couponApplied = false;
        var products = {!! json_encode($products) !!};

        @if(!is_null($couponData) && $couponData['applyAmount'])
            couponAmount = '{{ $couponData['applyAmount'] }}';
            couponCode = '{{ $couponData[0]['code'] }}';
            couponApplied = true;
        @endif

        function calculateTotal() {
            let cartTotal = tax = totalAmount = 0.00;

            $('.sub-total>input').each(function () {
                cartTotal += parseFloat($(this).val());
            });

            // $('#sub-total').text(currency_format(cartTotal.toFixed(2)));

            // calculate and display tax
            var totalTax = 0;
            $('.tax_detail').each(function () {
                // var tax_on_price = $(this).data('tax-on-price-status');
                var tax = $(this).closest('.tax_detail').find('.tax_amount').val();
                // totalTax += (tax_on_price == 'inactive') ? parseFloat(tax) : 0;
                totalTax += parseFloat(tax);

            });

            $('#tax').text(currency_format(totalTax));
            $('#sub-total').text(currency_format(cartTotal - totalTax));

            totalAmount = cartTotal;

            if(couponAmount)
            {
                if(totalAmount>=couponAmount)
                {
                    totalAmount = totalAmount - couponAmount;
                }
                else
                {
                    totalAmount = 0;
                }
            }
            // $('.sub-total>span').text(currency_format(totalAmount.toFixed(2)));
            $('#total').text(currency_format(totalAmount.toFixed(2)));
        }

        $('body').on('click', '#remove-coupon', function() {
            removeCoupon();
        });

        $('body').on('click', '.increase', function() {
            var input = $(this).siblings('input');
            var currentValue = input.val();
            const type = input.data('type');
            const dealId = input.data('deal-id');

            if(currentValue>0) {
                input.val(parseInt(currentValue) + 1);
                input.trigger('keyup');
            }
        });

        $('body').on('click', '.decrease', function() {
            var input = $(this).siblings('input');
            var currentValue = input.val();

            if (currentValue > 1) {
                input.val(parseInt(currentValue) - 1);
                input.trigger('keyup');
            }
        });

        $('body').on('click', '.delete-item', function() {
            let ele = $(this);
            let key = $(this).data('key');

            var url = '{{ route('front.deleteProduct', ':id') }}';
            url = url.replace(':id', key);

            $.easyAjax({
                url: url,
                type: 'POST',
                data: {_token: $("meta[name='csrf-token']").attr('content')},
                redirect: false,
                blockUI: false,
                disableButton: true,
                buttonSelector: "#clear-cart",
                success: function (response) {
                    if (response.status == 'success') {
                        if (response.action == "redirect") {
                            var message = "";
                            if (typeof response.message != "undefined") {
                                message += response.message;
                            }

                            $.showToastr(message, "success", {
                                positionClass: "toast-top-right"
                            });

                            setTimeout(function () {
                                window.location.href = response.url;
                            }, 1000);
                        } else{
                            // updateCoupon ();
                            $(ele).parents(`tr#${key}`).remove();
                            calculateTotal();
                            $('.cart-badge').text(response.productsCount);
                            products = response.products;
                        }
                    }
                }
            })
        });

        function updateCart(ele) {
            let data = {};
            let currentValue = ele.val();
            let type = ele.data('type');
            let max_order = ele.data('max-order');
            let unique_id = ele.data('id');
            let price = ele.data('price');

            let showError = false;

            $('input.qty').each(function () {
                const serviceId = $(this).data('id');
                products[serviceId].quantity = parseInt($(this).val());
            });

            if(type == 'deal' && parseInt(currentValue) > parseInt(max_order)) {
                showError = true;
                ele.val(parseInt(max_order));

                totalAmount = 0;
                $('input.qty').each(function () {
                    let quantity = $(this).val();
                    let price = $(this).data('price');
                    let id = $(this).data('id');

                    let subTotal = parseInt(quantity) * parseInt(price);
                    totalAmount += subTotal;
                    // setSubTotal(id,subTotal);
                });

                $('#total').text(currency_format(totalAmount.toFixed(2)));
            }

            data.showError = showError;
            data.products = products;
            data.currentValue = currentValue;
            data.type = type;
            data.max_order = max_order;
            data.unique_id = unique_id;
            data._token = '{{ csrf_token() }}';


            if($('input.qty').val()>=0 && $('input.qty').val()!='') {
                $.easyAjax({
                    url: '{{ route('front.updateCart') }}',
                    type: 'POST',
                    data: data,
                    container: '.section',
                    blockUI: false,
                    success:function(response){
                        console.log(response);
                        // updateCoupon();
                    }
                })
            }
        }

        function removeCoupon() {
            $.easyAjax({
                url: '{{ route('front.remove-coupon') }}',
                type: 'GET',
                blockUI: false,
                disableButton: true,
                buttonSelector: "#remove-coupon",
                success: function (response) {
                    couponApplied = false;
                    $('#coupon').val('');
                    $('#coupon_amount').val(0);
                    couponAmount = 0;
                    calculateTotal();
                    $('#couponDiscountBox').remove();
                    $('#removeCouponBox').addClass('d-none');
                    $('#applyCouponBox').removeClass('d-none');
                    $('#applyCouponBox').addClass('CouponBox');
                }
            })
        }

        $('body').on('click', '#apply-coupon', function() {
            let cartTotal = tax = totalAmount = 0.00;

            $('.sub-total>input').each(function () {
                cartTotal += parseFloat($(this).val());
            });

            $('#sub-total').text(currency_format(cartTotal.toFixed(2)));

            // calculate and display tax
            var totalTax = 0;
            @if($type=='booking')
                $('.tax_detail').each(function () {
                // var tax_on_price = $(this).data('tax-on-price-status');
                var tax = $(this).closest('.tax_detail').find('.tax_amount').val();
                // totalTax += (tax_on_price == 'inactive') ? parseFloat(tax) : 0;
                //
                totalTax += parseFloat(tax);
                });
                $('#tax').text(currency_format(totalTax));
            @endif

            totalAmount = cartTotal + totalTax;

           var couponVal = $('#coupon').val();

           if((couponVal === undefined || couponVal === "" || couponVal === null)){
               return $.showToastr("@lang('errors.coupon.required')", 'error');
           } else{
               $.easyAjax({
                    url: '{{ route('front.apply-coupon') }}',
                    type: 'GET',
                    data: {'coupon':couponVal},
                    blockUI: false,
                    disableButton: true,
                    buttonSelector: "#apply-coupon",
                    success: function (response) {
                        if(response.status != 'fail') {
                            couponApplied = true;
                            couponCode = couponVal;
                            couponAmount = response.amount;

                            if(couponAmount>totalAmount) {
                                couponAmount = totalAmount;
                            }

                            calculateTotal();
                            $('#couponDiscountBox').remove();
                            var discountElement = '<li id="couponDiscountBox">'+
                                '<span class="text-uppercase">'+
                                "@lang('app.discount') ("+response.couponData.code+'):'+
                                '</span>'+
                                '<span id="discountCoupon">-'+currency_format(couponAmount)+
                                '</span>'+
                                '</li>';
                            $(discountElement).insertBefore( "#totalAmountBox" );

                            $('#applyCouponBox').addClass('d-none');
                            $('#applyCouponBox').removeClass('CouponBox');

                            $('#removeCouponBox').removeClass('d-none');

                            $('#couponCodeAmonut').html(currency_format(couponAmount));
                            $('#couponCode').html(response.couponData.code);
                        } else{
                            removeCoupon ();
                        }
                    }
               })
           }
        });

        function updateCoupon() {

            let cartTotal = tax = totalAmount = 0.00;

            $('.sub-total>input').each(function () {
                cartTotal += parseFloat($(this).val());
            });

            $('#sub-total').text(currency_format(cartTotal.toFixed(2)));

            // calculate and display tax
            var totalTax = 0;
            @if($type=='booking')
                $('.tax_detail').each(function () {
                // var tax_on_price = $(this).data('tax-on-price-status');
                var tax = $(this).closest('.tax_detail').find('.tax_amount').val();
                // totalTax += (tax_on_price == 'inactive') ? parseFloat(tax) : 0;
                //
                totalTax += parseFloat(tax);
                });
                $('#tax').text(currency_format(totalTax));
            @endif

            totalAmount = cartTotal + totalTax;

            if (couponApplied) {

                $.easyAjax({
                    url: '{{ route('front.update-coupon') }}',
                    type: 'GET',
                    data: {'coupon': couponCode},
                    blockUI: false,
                    success: function (response) {
                        if (response.status != 'fail') {
                            couponAmount = response.amount;

                            if(couponAmount>totalAmount) {
                                couponAmount = totalAmount;
                            }

                            calculateTotal();
                            $('#couponDiscountBox').remove();
                            var discountElement = '<li id="couponDiscountBox">' +
                                '<span class="text-uppercase">' +
                                "@lang('app.discount') (" + response.couponData.code + '):' +
                                '</span>' +
                                '<span id="discountCoupon">-' + currency_format(couponAmount) +
                                '</span>' +
                                '</li>';
                            $(discountElement).insertBefore("#totalAmountBox");

                            $('#applyCouponBox').addClass('d-none');
                            $('#applyCouponBox').removeClass('CouponBox');

                            $('#removeCouponBox').removeClass('d-none');

                            $('#couponCodeAmonut').html(currency_format(couponAmount));
                            $('#couponCode').html(response.couponData.code);
                        } else {
                            removeCoupon();
                        }

                    }
                })
            }
        }

        $(document).on('keyup', 'input.qty', function () {
            const id = $(this).data('id');
            const price = $(this).data('price');

            // const tax_on_price_status = $(this).data('tax-on-price-status');
            const quantity = $(this).val();
            const el = $(this);

            const type = $(this).data('type');
            const dealId = $(this).data('deal-id');

            let taxPercent = $(`tr#${id}`).find('.tax_detail>.tax_percent').val();

            if (quantity<0) {
                $(this).val(1);
            }

            let taxAmount = price * (taxPercent/100) * quantity;

            let subTotal = price * quantity - taxAmount;

            clearTimeout(cartUpdate);

            /*if (quantity == '' || quantity == 0) {
                tax = price - (price * taxPercent / 100);
                subTotal = price - tax;
            }else {
                subTotal = price - (price * taxPercent / 100) * quantity;
                tax = tax * quantity
                subTotal = price * quantity - tax;
            }*/


            $(`tr#${id}`).find('.tax_detail>.tax_amount').val(taxAmount);

            setSubTotal(id, price * quantity, taxPercent, taxAmount);

            calculateTotal();

            cartUpdate = setTimeout(() => {
                updateCart($(this));
            }, 500);

        });

        $(document).on('blur', 'input.qty', function () {
            if ($(this).val() == '' || $(this).val() == 0) {
                $(this).val(1);
            }
        })

        $('body').on('click', '.go-back', function() {
            var url = "{{ route('front.index') }} ";
            window.location.href = url;
        });

        $('body').on('click', '.next-step', function() {

            @php
                $url = (!is_null($type) && $type == 'deal') ? route('front.checkoutPage') : route('front.bookingPage');
            @endphp

            var url = "{{ $url }}";
            window.location.href = url;
        });

        function setSubTotal(id, value, taxPercent, taxAmount){
            $(`tr#${id}`).find('.sub-total>input').val(value.toFixed(2));
            $(`tr#${id}`).find('.sub-total>span').text(currency_format(value.toFixed(2)));
            $(`tr#${id}`).find('.tax_percent>input').val(parseFloat(taxPercent).toFixed(2));
            $(`tr#${id}`).find('.tax_amount>span').text(currency_format(taxAmount.toFixed(2)));
        }

    </script>
    @include("partials.currency_format")
@endpush
