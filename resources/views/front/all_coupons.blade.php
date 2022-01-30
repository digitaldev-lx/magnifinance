@extends('layouts.front')

@push('styles')
    <link href=" {{ asset('front/css/all_deals.css') }} " rel="stylesheet">
    <style>
        h6{
            color: aliceblue;
            font-size: 0.8em;
            margin-top: -5px;
            padding-bottom: 5px;
        }
    </style>
@endpush

@section('content')

    <!-- BREADCRUMB START -->
    <section class="breadcrumb_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 col-md-5">
                    <h1 class="mb-0"> @lang('front.allCoupons') </h1>
                </div>
                <div class="col-lg-3 col-md-7">
                    <nav>
                        <ol class="breadcrumb mb-0 justify-content-center">
                            <li class="breadcrumb-item"><a href="/"> @lang('app.home') </a></li>
                            <li class="breadcrumb-item active"><span>@lang('front.allCoupons')</span></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </section>
    <!-- BREADCRUMB END -->

    <!-- ALL DEALS START -->
    <section class="all_deals_section">
        <div class="container">
            <div class="row">
                <div class="col-md-4 d-none d-lg-block">
                    <!-- FILTER START -->
                    <div class="filter_heading d-flex justify-content-between">
                        <h2 class="mb-0">@lang('app.filter')</h2>
                        <div class="clear_all_filter">
                            <div class="clear_effect"></div>
                            <a class="d-flex align-items-center justify-content-center" href=""><i class="zmdi zmdi-close"></i> @lang('front.clearAll')</a>
                        </div>
                    </div>
                    <div class="filter_types">

                            <div class="card">
                                <div class="card-header">
                                    <button class="card-link" type="button" data-toggle="collapse" data-target="#seller" aria-expanded="true">@lang('app.seller')</button>
                                </div>
                                <div id="seller" class="collapse show">
                                    <div class="card-body">


                                    </div>
                                </div>
                            </div><!-- SELLER CARD END -->

                            <div class="card">
                                <div class="card-header">
                                    <button class="card-link" type="button" data-toggle="collapse" data-target="#discount" aria-expanded="true">@lang('app.discount')</button>
                                </div>
                                <div id="discount" class="collapse show">
                                    <div class="card-body">
                                        <input id='10p_m' type='checkbox' name="discount[]" value="0-24" class="discounts" />
                                            <label for='10p_m' class="mb-3">
                                                <span></span>10% @lang('modules.filter.flatDiscount')
                                            </label><!-- SINGLE CATEGORY END -->

                                        <input id='25p_m' type='checkbox' name="discount[]" value="25-34" class="discounts"/>
                                            <label for='25p_m' class="mb-3">
                                                <span></span>25%  @lang('modules.filter.flatDiscount')
                                            </label><!-- SINGLE CATEGORY END -->

                                        <input id='35p_m' type='checkbox' name="discount[]" value="35-49" class="discounts"/>
                                            <label for='35p_m' class="mb-3">
                                                <span></span>35%  @lang('modules.filter.flatDiscount')
                                            </label><!-- SINGLE CATEGORY END -->

                                        <input id='50p_m' type='checkbox' name="discount[]" value="50-100" class="discounts"/>
                                            <label for='50p_m' class="mb-3">
                                                <span></span>50%  @lang('modules.filter.flatDiscount')
                                            </label><!-- SINGLE CATEGORY END -->
                                    </div>
                                </div>
                            </div><!-- DISCOUNT CARD END -->


                    </div>
                    <!-- FILTER END -->
                </div>
                <div class="col-md-8">
                    <!-- DEAL START -->
                    <div class="sort_box d-flex justify-content-between">
                        <div class="col-md-6 d-flex align-items-center mobile-no-padding">
                            <p id="filtered_coupon_count">@lang('app.showing') {{$coupons->count()}} @lang('app.of') {{$coupons->total()}} @lang('app.results')</p>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end position-relative mobile-no-padding">
                            <div class="input-group">
                                <div class="input-group-append location_icon">
                                    <span class="input-group-text">@lang('front.sortBy'):</span>
                                </div>
                                <select class="myselect" name="sort_by" id="sort_by" onchange="filter()">
                                    <option value="">@lang('app.choose')</option>
                                    <option value="low_to_high">@lang('front.lowToHigh')</option>
                                    <option value="high_to_low">@lang('front.highToLow')</option>
                                    <option value="newest">@lang('front.newest')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="filtered_coupons">

                        <div class="mt-4 d-flex flex-wrap">

                            @foreach ($coupons as $coupon)
                                <div class="col-lg-6 col-md-12 d-flex mb-4 mobile-no-padding">
                                    <div class="media coupon_box view_latest_coupon_box">
                                        <div class="coupon_discount align-self-center">
                                        <p class="mb-2">vsdvdvdsvdsv</p>
                                            <i class="zmdi zmdi-hc-lg zmdi-star-outline align-self-center"></i>
                                        </div>
                                        <div class="media-body coupon_code_box text-center position-relative">
                                            <h6>
                                                @if (!is_null($coupon->amount) && is_null($coupon->percent))
                                                {{$coupon->amount}}
                                            @elseif (is_null($coupon->amount) && !is_null($coupon->percent))
                                                {{$coupon->amount}}%
                                            @else
                                                @lang('app.maxAmountOrPercent', ['percent' => $coupon->percent, 'maxAmount' => currencyFormatter($coupon->amount)])
                                            @endif
                                            <br><span>@lang('app.off')</span>
                                            </h6>
                                            <a href="javascript:;" id="coupon_one" class="show_latest_coupon_code show-coupon" data-coupon-id="{{$coupon->id}}" data-coupon-title="{{$coupon->title}}" data-coupon-description="{{$coupon->description}}" data-coupon-code="{{$coupon->code}}">@lang('app.show') @lang('app.code')</a>
                                            <p class="mb-0"><i class="zmdi zmdi-time"></i>
                                                @lang('app.expireOn')
                                                {{  \Carbon\Carbon::parse($coupon->end_date_time)->translatedFormat('d-m-Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        <div class="deals_pagination mt-4 d-flex justify-content-center" id="pagination">
                            {{ $coupons->links() }}
                        </div>

                    </div>
                    <!-- DEAL END -->
                </div>
            </div>
        </div>
    </section>
    <!-- ALL DEALS END -->

    <!-- FILTER MOBILE MODAL START -->
    <div id="modal-container">
        <div class="modal-background">
            <div class="modal ">
                <span class="close_filter_modal">&times;</span>
                <div class="filter_heading d-flex justify-content-between">
                    <h2 class="mb-0">@lang('app.filter')</h2>
                    <div class="clear_all_filter">
                        <div class="clear_effect"></div>
                        <a class="d-flex align-items-center justify-content-center" href=""><i class="zmdi zmdi-close"></i> @lang('front.clearAll')</a>
                    </div>
                </div>
                <div class="filter_types">

                    <div class="card">
                        <div class="card-header">
                            <button class="card-link" type="button" data-toggle="collapse" data-target="#seller" aria-expanded="true">@lang('app.seller')</button>
                        </div>
                        <div id="seller" class="collapse show">
                            <div class="card-body">



                            </div>
                        </div>
                    </div><!-- SELLER CARD END -->

                    <div class="card">
                        <div class="card-header">
                            <button class="card-link" type="button" data-toggle="collapse" data-target="#discount" aria-expanded="true">@lang('app.discount')</button>
                        </div>
                        <div id="discount" class="collapse show">
                            <div class="card-body">
                                <input id='10p' type='checkbox' name="discount[]" value="0-24" class="discounts" />
                                    <label for='10p' class="mb-3">
                                        <span></span>10%  @lang('modules.filter.flatDiscount')
                                    </label><!-- SINGLE CATEGORY END -->

                                <input id='25p' type='checkbox' name="discount[]" value="25-34" class="discounts"/>
                                    <label for='25p' class="mb-3">
                                        <span></span>25%  @lang('modules.filter.flatDiscount')
                                    </label><!-- SINGLE CATEGORY END -->

                                <input id='35p' type='checkbox' name="discount[]" value="35-49" class="discounts"/>
                                    <label for='35p' class="mb-3">
                                        <span></span>35%  @lang('modules.filter.flatDiscount')
                                    </label><!-- SINGLE CATEGORY END -->

                                <input id='50p' type='checkbox' name="discount[]" value="50-100" class="discounts"/>
                                    <label for='50p' class="mb-3">
                                        <span></span>50%  @lang('modules.filter.flatDiscount')
                                    </label><!-- SINGLE CATEGORY END -->
                            </div>
                        </div>
                    </div><!-- DISCOUNT CARD END -->


                </div>
            </div>
        </div>
    </div>
    <!-- FILTER MOBILE MODAL END -->

    <div class="footer_mobile_filter w-100 py-3 text-center d-lg-none d-md-block d-sm-block">
        <div id="filter_modal" class="filter_modal_wrapper">@lang('app.filter')&nbsp;&nbsp;<i class="zmdi zmdi-filter-list"></i></div>
    </div>

@endsection

@push('footer-script')
    <script>

        $(".discounts").on('click', function() {
            var $box = $(this);
            if ($box.is(":checked")) {
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
                $(group).prop("checked", false);
                $box.prop("checked", true);
            } else {
                $box.prop("checked", false);
            }

            filter();
        });

        function filter()
        {
            var companies = [];
            var discounts = [];

            $.each($("input[name='companies[]']:checked"), function(){
                companies.push($(this).val());
            });

            $.each($("input[name='discount[]']:checked"), function(){
                discounts.push($(this).val());
            });

            $.easyAjax({
                url: '{{ route('front.allCoupons') }}',
                type: 'GET',
                data: {companies : companies.join(","), discounts : discounts, sort_by : $('#sort_by').val()},
                success: function (response) {
                    if(response.coupon_count==0) {
                        let image_path = '{{ asset("front/images/no-search-result.png") }}';
                        $('#filtered_coupons').html('<div  class="mt-4 no_result text-center"><img src="{{asset('front/images/pixel.gif')}}" data-src="'+image_path+'" class="mx-auto d-block" alt="Image" width="40%"/><h2 class="mt-3">@lang("messages.noResultFound") :(</h2><p>@lang("messages.checkSpellingOrUseGeneralTerms")</p></div>');
                    }
                    else {
                        $('#filtered_coupons').html(response.view);
                    }
                    $('#filtered_coupon_count').html('@lang("app.showing") '+response.coupon_count+' @lang("app.of") '+response.coupon_total+' @lang("app.results") ');
                    lazyload();
                }
            });
        } /* end of filter */


        $('body').on('click', '#pagination a', function(e)
        {
            e.preventDefault();

            var companies = [];
            var discounts = [];
            var page = $(this).attr('href').split('page=')[1];

            $.each($("input[name='companies[]']:checked"), function(){
                companies.push($(this).val());
            });

            $.each($("input[name='discount[]']:checked"), function(){
                discounts.push($(this).val());
            });

            var url = '{{ route("front.allCoupons") }}?page='+page+'&companies='+companies+'&discounts='+discounts+'&sort_by='+$('#sort_by').val();
            $.get(url, function(response){
                $('#filtered_coupons').html(response.view);
                $('#filtered_coupon_count').html('Showing '+response.coupon_count+' of '+response.coupon_total+' results');
            });
        });

    </script>
@endpush





