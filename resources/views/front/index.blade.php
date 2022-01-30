@extends('layouts.front')

@push('styles')
    <link href=" {{ asset('front/css/article_detail.css') }} " rel="stylesheet">
<!--    <link href=" {{ asset('front/css/all_deals.css') }} " rel="stylesheet">
    <link href=" {{ asset('front/css/service_detail.css') }} " rel="stylesheet">-->
    <style>
        .featured_deal_imgBox {
            height: 236px;
            overflow: hidden;
        }
        .rupee {
            font-size: 26px !important;
            font-weight: 500;
            line-height: 0 !important;
            color: #fff !important;
        }
    </style>
@endpush

@section('content')

    <!-- SLIDER START -->
        @if (array_search('Slider Section', array_column($sections, 'name')) !== false && $sliderContents->count() > 0)
            <section class="position-relative bannerSection">
                <div class="container-fluid">
                    <div class="row">
                        <div class="owl-carousel owl-theme" id="banner_slider">
                            @foreach ($sliderContents as $sliderContent)
                            <div class="item">
                                <div class="banner_img1" style="background-image: url({{ $sliderContent->image_url }});">
                                    @if ($sliderContent->content != '' || $sliderContent->action_button != '')
                                    <div class="container">
                                        <div class="item-inner itemBox-{{ $sliderContent->content_alignment }} text-center">
                                            <div class="itemBox text-center">
                                               @if ($sliderContent->subheading) <h2>{{ $sliderContent->subheading }}</h2> @endif
                                               @if ($sliderContent->heading) <h1>{{ $sliderContent->heading }}</h1> @endif
                                                {!! $sliderContent->content !!}
                                            </div>

                                            <a href="{{ $sliderContent->url }}" @if ($sliderContent->open_tab == 'new') target="_blank" @endif class="login draw-border btn">{{ ucwords($sliderContent->action_button) }}</a>

                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
    <!-- SLIDER END -->

    <!-- FEATURED DEALS START -->
        @if (array_search('Deal Section', array_column($sections, 'name')) !== false)
            <section class="featuredSection" id="featuredDeals">
                <div class="container">
                    <div class="heading">
                        <p class="mb-0">@lang('front.featuredDeals')</p>
                    </div>
                    <div class="row">
                        <div class="owl-carousel owl-theme" id="featured_deal_slider">
                            {{-- Placeholder that will show until data didn't load --}}
                            @for ($i = 0; $i < 2; $i++)
                            <div class="ph-item deal_item">
                                <div class="ph-col-6 ph-card-image"></div>
                                <div class="py-2 pl-2 pr-2">
                                    <div class="ph-row">
                                        <div class="ph-col-12 big"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12 big"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12 big"></div>
                                        <div class="ph-col-12 big"></div>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                    {{-- <div class="row justify-content-center mt-3" id="view_all_deals_btn">
                        <a href="{{ route('front.deals') }}" class="view_all hvr-radial-out">@lang('app.viewAll')</a>
                    </div> --}}
                </div>
            </section>
        @endif
    <!-- FEATURED DEALS END -->

    <!-- CHOOSE YOUR CATEGORY START -->
        @if (array_search('Category Section', array_column($sections, 'name')) !== false)
            <section class="categorySection" id="categorySection">
                <div class="container">
                    <div class="heading justify-content-lg-center">
                        <p class="mb-0 ">@lang('front.chooseYourCategory')</p>
                    </div>
                    <div class="row">
                        {{-- Placeholder that will show until data didn't load --}}
                        @for ($i = 0; $i < 8; $i++)
                            <div class="col-md-3 col-6 mb-4">
                                <div class="ph-item">
                                    <div class="ph-col-12">
                                        <div class="ph-picture"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor

                    </div>
                </div>
            </section>
        @endif
    <!-- CHOOSE YOUR CATEGORY END -->

    <!-- LATEST COUPONS START -->
        @if (array_search('Coupon Section', array_column($sections, 'name')) !== false && $coupons->count() > 0)
            <section class="couponSection">
                <div class="container">
                    <div class="heading">
                        <p class="mb-0">@lang('front.latestCoupons')</p>
                    </div>
                    <div class="">
                        <div class="owl-carousel owl-theme" id="latest_coupon_slider">
                            @foreach ($coupons as $coupon)
                                @if ($loop->iteration%6==1 || $loop->first) <div class="item"><div class="row"> @endif
                                <div class="col-lg-4 col-md-6 d-flex mb-4">
                                    <div class="media coupon_box">
                                        <div class="coupon_discount align-self-center">
                                            <h2 class="mb-1">
                                                {{$coupon->title}}
                                            </h2>
                                            <p class="mb-0"><i class="zmdi zmdi-time"></i>
                                                @lang('app.expireOn')
                                                {{  \Carbon\Carbon::parse($coupon->end_date_time)->translatedFormat($settings->date_format) }}
                                            </p>
                                        </div>
                                        <div class="media-body coupon_code_box text-center position-relative">
                                            <h2>
                                                @if (!is_null($coupon->amount) && $coupon->discount_type === 'percentage')
                                                    <span class="rupee">{{$coupon->amount}}%</span>
                                                    <br><span>@lang('app.off')</span>
                                                @elseif(!is_null($coupon->amount) && $coupon->discount_type === 'amount')
                                                    <span class="rupee">{{$settings->currency->currency_symbol}}{{$coupon->amount}}</span>
                                                    <br><span>@lang('app.off')</span>
                                                @endif
                                            </h2>
                                            <a href="javascript:;" id="coupon_one" class="show_latest_coupon_code show-coupon" data-coupon-id="{{$coupon->id}}" data-coupon-title="{{$coupon->title}}" data-coupon-description="{{$coupon->description}}" data-coupon-code="{{$coupon->code}}"> @lang('app.show') @lang('app.code') </a>
                                        </div>
                                    </div>
                                </div>
                                @if ($loop->iteration%6==0 || $loop->last)
                                    </div></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif
    <!-- LATEST COUPONS END -->

    <!-- SPOTLIGHT START -->
        @if (array_search('Spotlight Section', array_column($sections, 'name')) !== false && $coupons->count() > 0)
            <section class="spotlightSection position-relative" id="spotlightSection">
                <div class="container">
                    <div class="heading">
                        <p class="mb-0">@lang('app.in') @lang('menu.spotlight')</p>
                    </div>
                </div>
                <div class="container">
                    <div class="">
                        <div class="owl-carousel owl-theme" id="spotlight_slider">
                            {{-- Placeholder that will show until data didn't load --}}
                            @for ($i = 0; $i < 4; $i++)
                                <div class="item spot_box">
                                    <div class="ph-item">
                                        <div class="ph-col-12 ph-card-image"></div>
                                        <div class="pl-2 pr-2 mt-2">
                                            <div class="ph-row">
                                                <div class="ph-col-12 big"></div>
                                                <div class="ph-col-12 big"></div>
                                                <div class="ph-col-12"></div>
                                                <div class="ph-col-12"></div>
                                                <div class="ph-col-12"></div>
                                                <div class="ph-col-12 big"></div>
                                                <div class="ph-col-12"></div>
                                                <div class="ph-col-12"></div>
                                                <div class="ph-col-12 big"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor

                        </div>
                    </div>
                </div>
            </section>
        @endif
    <!-- SPOTLIGHT END -->
    @if (array_search('Blog Section', array_column($sections, 'name')) !== false && $coupons->count() > 0)
    <section class="relatedArticlesSection position-relative" id="relatedArticlesSection">
        <div class="container">
            <div class="heading">
                <p class="mb-0">{{__('menu.latestArticles')}}</p>
            </div>
        </div>
        <div class="container">
            <div class="">
                <div class="owl-carousel owl-theme" id="articles_slider">
                    {{-- Placeholder that will show until data didn't load --}}
                    @for ($i = 0; $i < 4; $i++)
                        <div class="item spot_box">
                            <div class="ph-item">
                                <div class="ph-col-12 ph-card-image"></div>
                                <div class="pl-2 pr-2 mt-2">
                                    <div class="ph-row">
                                        <div class="ph-col-12 big"></div>
                                        <div class="ph-col-12 big"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12 big"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12"></div>
                                        <div class="ph-col-12 big"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor

                </div>
            </div>
        </div>
    </section>
    <!-- RELATED ARTICLES END -->
    @endif

@endsection

@push('footer-script')
    <script type="text/javascript">
        $(function () {
            ajax();
        });

    </script>
@endpush

