@extends('layouts.front')

@push('styles')
    <link href=" {{ asset('front/css/service_detail.css') }} " rel="stylesheet">
    <style>
        .owl-carousel .owl-dots.disabled, .owl-carousel .owl-nav.disabled {
            display: none !important;
        }
    </style>
@endpush

@section('content')

    <!-- BREADCRUMB START -->
    <section class="breadcrumb_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-5">
                    <h1 class="mb-0"> {{ $service->company->company_name }} </h1>
                </div>
                <div class="col-lg-6 col-md-7 d-none d-lg-block d-md-block">
                    <nav>
                        <ol class="breadcrumb mb-0 justify-content-center">
                            <li class="breadcrumb-item"><a href="/">@lang('app.home')</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('front.services', 'all') }}">@lang('front.allServices')</a></li>
                            <li class="breadcrumb-item active"><span> {{ \Illuminate\Support\Str::limit(ucwords($service->name), 20 , '...') }} </span></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <!-- BREADCRUMB END -->

    <!-- SERVICE DETAIL START -->
    <section class="deal_detail_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-12">
                    <div class="owl-carousel owl-theme" id="deal_detail_slider">

                        @php $count = 0 @endphp
                        @forelse($service->image ?: [] as $image)
                            <div class="item">
                                <div class="deal_detail_img position-relative">
                                    <img src="{{asset('front/images/pixel.gif')}}" data-src="{{ cdn_storage_url($image) }}" alt="{{$service->name}}" />
                                </div>
                            </div>
                            @php $count++ @endphp
                        @empty
                            <div class="item">
                                <div class="deal_detail_img position-relative">
                                    <img src="{{asset('front/images/pixel.gif')}}" data-src="{{ asset('front/images/deal_detail/sizzlin.png') }}" alt="Image" />
                                </div>
                            </div>
                            <div class="item">
                                <div class="deal_detail_img position-relative">
                                    <img src="{{asset('front/images/pixel.gif')}}" data-src="{{ asset('front/images/deal_detail/sizzlin.png') }}" alt="Image" />
                                </div>
                            </div>
                        @endforelse


                    </div>
                </div>
                <div class="col-lg-5 col-md-12 deal_detail_box">
                    <a href="{{route('front.vendorPage',$service->company->slug)}}"><h3 class="mt-lg-1 mt-4">{{ $service->company->company_name }}</h3></a>
                    <h2>{{ $service->name }}</h2>
                    <div class="deal_detail_contact">
                        <span><i class="zmdi zmdi-time"></i>&nbsp;&nbsp;{{ $service->time }} {{ $service->time_type }}</span>
                        &nbsp;&nbsp;|&nbsp;&nbsp;
                        <a href="tel:{{ $service->company->company_phone }}"><i class="zmdi zmdi-phone"></i>&nbsp;&nbsp;{{ $service->company->company_phone }}</a> &nbsp;&nbsp;|&nbsp;&nbsp;
                        <span><i class="zmdi zmdi-pin"></i>&nbsp;&nbsp;{{ $service->location->name }}</span>
                        @if ($service->ratings->count() > 0 && $settings->rating_status == 'active')
                            <br><br>
                            <i class="feedback_stars zmdi @if($service->ratings->sum('rating')/$service->ratings->count() >= 1) zmdi-star @else zmdi-star-outline @endif align-self-center"></i>
                            <i class="feedback_stars zmdi @if($service->ratings->sum('rating')/$service->ratings->count() >= 2) zmdi-star @else zmdi-star-outline @endif align-self-center"></i>
                            <i class="feedback_stars zmdi @if($service->ratings->sum('rating')/$service->ratings->count() >= 3) zmdi-star @else zmdi-star-outline @endif align-self-center"></i>
                            <i class="feedback_stars zmdi @if($service->ratings->sum('rating')/$service->ratings->count() >= 4) zmdi-star @else zmdi-star-outline @endif align-self-center"></i>
                            <i class="feedback_stars zmdi @if($service->ratings->sum('rating')/$service->ratings->count() >= 5) zmdi-star @else zmdi-star-outline @endif align-self-center"></i>
                            ({{ $service->ratings->count() }})
                        @endif
                    </div>
                    <div class="deal_detail_offer_with_price d-flex align-items-center">
                        @if($service->discount > 0)
                            <i>
                                @if($service->discount_type=='percent')
                                    {{$service->discount}}%
                                @else
                                    {{ currencyFormatter($service->discount) }}
                                @endif
                            @lang('app.off')</i>
                        @endif
                        <p>{{ $service->formated_discounted_price }}
                            <span>@if($service->discount > 0){{ $service->formated_price }}@endif</span></p>
                    </div>
                    <div class="deal_detail_expiry_date">
                    </div>
                    <div class="form_with_buy_deal d-lg-flex d-md-flex d-block">
                        <form class="mb-lg-0 mb-md-0 mb-4">
                            <div class="value-button" id="decrease" value="Decrease Value"><i class="zmdi zmdi-minus"></i></div>

                            @php $product = current($reqProduct); @endphp

                            <input type="number" id="number" name="qty" value="{{ sizeof($reqProduct) > 0 ? $product['quantity'] : 1 }}" size="4" title="Quantity" class="input-text qty" data-id="{{ $service->id }}" data-price="{{$service->converted_price}}" autocomplete="none" min="1" />

                            <div class="value-button" id="increase" value="Increase Value"><i class="zmdi zmdi-plus"></i></div>
                        </form>
                        <div class="add @if(sizeof($reqProduct) == 0) d-flex @else d-none @endif w-100">
                            <button class="btn btn-custom added-to-cart ml-lg-3 ml-md-3 ml-0" id="add-item">
                                    @lang('front.addItem')
                            </button>
                        </div>
                        <div class="update @if(sizeof($reqProduct) > 0) d-flex @else d-none @endif w-100">
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
                    {!! $service->description !!}
                </div>
            </div>
        </div>
    </section>
    <!-- SERVICE DETAIL END -->

@endsection

@push('footer-script')
    <script>

        $('body').on('click', '#increase', function() {
            var currentValue = $('#number').val();
            $('#number').val(parseInt(currentValue) + 1);
        });

        $('body').on('click', '#decrease', function() {
            var currentValue = $('#number').val();
            if (currentValue > 1) {
                $('#number').val(parseInt(currentValue) - 1);
            }
        });

        $('body').on('click', '#delete-product', function() {
            let key = $('input.qty').data('id');

            var url = '{{ route('front.deleteFrontProduct', ':id') }}';
            url = url.replace(':id', 'service'+key);

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
            let type = 'service';
            let unique_id = '{{ $service->id }}';
            let id = '{{ $service->id }}';
            let price = '{{ $service->converted_discounted_price }}';
            let name = '{{ $service->name }}';
            let companyId = '{{ $service->company->id }}';
            let quantity = $('#number').val();

            var data = {id, type, price, name, companyId, quantity, unique_id, _token: $("meta[name='csrf-token']").attr('content')};

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
                        }).then((willDelete) => {
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
