@extends('layouts.front')

@push('styles')
    <link href=" {{ asset('front/css/deal_detail.css') }} " rel="stylesheet">
@endpush

@section('content')
    <!-- BREADCRUMB START -->
        <section class="breadcrumb_section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-5">
                        <h1 class="mb-0">{{ $deal->company->company_name }}</h1>
                    </div>
                    <div class="col-lg-6 col-md-7 d-none d-lg-block d-md-block">
                        <nav>
                            <ol class="breadcrumb mb-0 justify-content-center">
                                <li class="breadcrumb-item"><a href="/"> @lang('app.home')</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('front.deals') }}">@lang('front.deals')</a></li>
                                <li class="breadcrumb-item active"><span>{{ $deal->title }}</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
    <!-- BREADCRUMB END -->

    <!-- DEAL DETAIL START -->
        <section class="deal_detail_section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 col-md-12">
                        <div class="deal_detail_img position-relative">
                            <img src="{{asset('front/images/pixel.gif')}}" data-src=" {{ $deal->deal_image_url }} " alt="Image" />
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12 deal_detail_box">
                        <a href="{{route('front.vendorPage',$deal->company->slug)}}"><h3 class="mt-lg-1 mt-4">{{ $deal->company->company_name }}</h3></a>
                        <h2>{{ $deal->title }}</h2>
                        <div class="deal_detail_contact">
                            <a href="tel:{{ $deal->company->company_phone }}"><i class="zmdi zmdi-phone"></i>&nbsp;&nbsp;{{ $deal->company->company_phone }}</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                            <span><i class="zmdi zmdi-pin"></i>&nbsp;&nbsp;{{ $deal->location->name }}</span>&nbsp;&nbsp;
                        </div>
                        <div class="deal_detail_offer_with_price d-flex align-items-center">
                            @if($deal->converted_original_amount - $deal->converted_deal_amount > 0)
                            <i>
                                @if($deal->discount_type=='percentage')
                                    {{$deal->percentage}}%
                                @else
                                {{currencyFormatter($deal->converted_original_amount - $deal->converted_deal_amount)}}
                                @endif
                                @lang('app.off')
                            </i>
                            @endif
                            <p>{{ $deal->formated_deal_amount }}
                                <span>@if($deal->converted_deal_amount < $deal->converted_original_amount){{ $deal->formated_original_amount }}@endif</span>
                            </p>
                        </div>
                        <div class="deal_detail_expiry_date">
                            <p>
                                <span>@lang('app.expireOn') : </span>
                                {{ \Carbon\Carbon::parse($deal->end_date_time)->translatedFormat($settings->date_format.', '.$settings->time_format) }}
                            </p>
                        </div>
                        <div class="form_with_buy_deal d-lg-flex d-md-flex d-block">
                            @if ($deal->max_order_per_customer > 1)
                            <form class="mb-lg-0 mb-md-0 mb-4">
                                <div class="value-button" id="decrease" value="Decrease Value"><i class="zmdi zmdi-minus"></i></div>
                                    <input
                                    type="number"
                                    id="number"
                                    name="qty"
                                    size="4"
                                    title="Quantity"
                                    class="input-text qty"
                                    autocomplete="none"
                                    @if(sizeof($reqProduct) == 0) value="1" @else value="{{$reqProduct['deal'.$deal->id]['quantity']}}" @endif
                                    min="1"
                                    readonly
                                    data-id="{{ $deal->id }}"
                                    data-max-order="{{ $deal->max_order_per_customer }}"
                                     />
                                <div class="value-button" id="increase" value="Increase Value"><i class="zmdi zmdi-plus"></i></div>
                            </form>
                            @endif
                            <div class="add @if(sizeof($reqProduct) == 0) d-flex @else d-none @endif w-100">
                                <button class="btn btn-custom added-to-cart ml-lg-3 ml-md-3 ml-0" id="add-item">
                                     @lang('front.addItem')
                                </button>
                            </div>
                            <div class="update mt-2 mt-lg-0 mt-md-0 @if(sizeof($reqProduct) > 0) d-flex @else d-none @endif w-100">
                                <button class="btn btn-custom update-cart ml-lg-3 ml-md-3 ml-0" id="update-item">
                                     @lang('front.buttons.updateCart')
                                </button>
                                <button class="btn btn-custom ml-3 btn-danger" id="delete-product">
                                     @lang('front.table.deleteProduct')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 deal_detail_content">
                        {!! $deal->description !!}
                    </div>
                </div>
            </div>
        </section>
    <!-- DEAL DETAIL END -->
@endsection

@push('footer-script')
    <script>
        $('body').on('click', '#increase', function() {
            var value = parseInt(document.getElementById('number').value, 10);
            value = isNaN(value) ? 0 : value;

            if(value<parseInt({{$deal->max_order_per_customer}})) {
                value++;
            } else if('{{$deal->max_order_per_customer}}'=='Infinite') {
                value++;
            } else {
                return toastr.error('{{ __("app.maxDealMessage", ["quantity" => $deal->max_order_per_customer]) }}');
            }
            document.getElementById('number').value = value;
        });

        $('body').on('click', '#decrease', function() {
            var value = parseInt(document.getElementById('number').value, 10);
            value = isNaN(value) ? 1 : value;
            value < 1 ? value = 1 : '';
            if (value > 1) {
                value--;
                document.getElementById('number').value = value;
            }
        });

        $('body').on('click', '#delete-product', function() {
            let key = $('input.qty').data('id');

            var url = '{{ route('front.deleteFrontProduct', ':id') }}';
            url = url.replace(':id', 'deal'+key);

            $.easyAjax({
                url: url,
                type: 'POST',
                data: {_token: $("meta[name='csrf-token']").attr('content')},
                redirect: false,
                blockUI: false,
                disableButton: true,
                buttonSelector: "#delete-product",
                success: function (response) {
                    $('.cart-badge').text(response.productsCount);

                    $('.add').addClass('d-flex');
                    $('.add').removeClass('d-none');

                    $('.update').removeClass('d-flex');
                    $('.update').addClass('d-none');

                    $('input.qty').val(1);
                }
            })
        });

        $('input.qty').on('blur', function () {
            if ($(this).val() == '' || $(this).val() == 0) {
                $(this).val(1);
            }
        });

        // add items to cart
        $('body').on('click', '.added-to-cart, .update-cart', function () {
            let element_id = $(this).attr('id');
            let max_order = "{{ $deal->max_order_per_customer }}";
            let type = 'deal';
            let unique_id = 'deal'+'{{ $deal->id }}';
            let id = '{{ $deal->id }}';
            let price = '{{  $deal->converted_deal_amount }}';
            let name = '{{ $deal->title }}';
            let companyId = '{{ $deal->company->id }}';
            let quantity = $('#number').val();

            var data = {id, type, price, name, companyId, quantity, unique_id, max_order, _token: $("meta[name='csrf-token']").attr('content')};

            $.easyAjax({
                url: '{{ route('front.addOrUpdateProduct') }}',
                type: 'POST',
                data: data,
                blockUI: false,
                disableButton: true,
                buttonSelector: "#"+element_id,
                success: function (response) {
                    if(response.result=='fail')
                    {
                        swal({
                            title: "@lang('front.buttons.clearCart')?",
                            text: "@lang('messages.front.errors.differentItemFound')",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        })
                        .then((willDelete) => {
                            if (willDelete)
                            {
                                var url = '{{ route('front.deleteProduct', ':id') }}';
                                url = url.replace(':id', 'all');

                                $.easyAjax({
                                    url: url,
                                    type: 'POST',
                                    data: {_token: $("meta[name='csrf-token']").attr('content')},
                                    redirect: false,
                                    success: function (response) {
                                        if (response.status == 'success') {
                                            $.easyAjax({
                                                url: '{{ route('front.addOrUpdateProduct') }}',
                                                type: 'POST',
                                                data: data,
                                                success: function (response) {
                                                    $('.cart-badge').text(response.productsCount);
                                                }
                                            })
                                        }
                                    }
                                })
                            }
                        });
                    }
                    $('.cart-badge').text(response.productsCount);

                    if (response.productsCount > 0) {
                        $('.add').removeClass('d-flex');
                        $('.add').addClass('d-none');

                        $('.update').addClass('d-flex');
                        $('.update').removeClass('d-none');
                    }
                }
            })
        });
    </script>
@endpush
