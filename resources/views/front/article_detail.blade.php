@extends('layouts.front')

@push('styles')
    <link href=" {{ asset('front/css/article_detail.css') }} " rel="stylesheet">
    <link href=" {{ asset('front/css/all_deals.css') }} " rel="stylesheet">
    <link href=" {{ asset('front/css/service_detail.css') }} " rel="stylesheet">
    <style>
        .adsImage {
            width: 100%;
            /*height: 100%;*/
            border-radius: 7px;
        }

        /*.deal_detail_expiry_date {
             margin: unset!important;
            font-family: 'Lato', sans-serif;
            font-weight: normal;
            font-stretch: normal;
            font-style: normal;
            line-height: 1.63;
            letter-spacing: normal;
            color: #2d2d2d;
            font-size: 16px;
            position: absolute;
            bottom: 0px;
            width: 100%;
        }*/
    </style>
@endpush

@section('content')
    <!-- BREADCRUMB START -->
    <section class="breadcrumb_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-5">
                    <h1 class="mb-0">{{__('menu.blog')}}</h1>
                </div>
                <div class="col-lg-6 col-md-7 d-none d-lg-block d-md-block">
                    <nav>
                        <ol class="breadcrumb mb-0 justify-content-center">
                            <li class="breadcrumb-item"><a href="/"> @lang('app.home')</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('front.blog') }}">{{__('menu.blog')}}</a></li>
                            <li class="breadcrumb-item active"><span title="{{$article->title}}">{{ \Illuminate\Support\Str::limit(ucwords($article->title), 30 , '...') }}</span></li>
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
                <div class="col-md-12">
                    <div class="deal_detail_img position-relative">
                        <img src="{{asset('front/images/pixel.gif')}}" data-src=" {{ $article->article_image_url }} "
                             alt="Image"/>
                    </div>
                </div>
                <div class="col-md-12 deal_detail_box">
                    <h2>{{ $article->title }}</h2>
                    @if(is_null($article->company) || !isset($article->company))
                        <a href="#"><h3 class="mt-lg-1 mt-4">SpotB</h3></a>
                    @else
                        <a href="{{route('front.vendorPage',$article->company->slug)}}"><h3
                                class="mt-lg-1 mt-4">{{ $article->company->company_name }}</h3></a>
                @endif

                <!--                        <div class="deal_detail_contact">
{{--                            <a href="tel:{{ $article->company->company_phone }}"><i class="zmdi zmdi-phone"></i>&nbsp;&nbsp;{{ $article->company->company_phone }}</a>&nbsp;&nbsp;|&nbsp;&nbsp;--}}
                {{--                            <span><i class="zmdi zmdi-pin"></i>&nbsp;&nbsp;{{ $article->location->name }}</span>&nbsp;&nbsp;--}}
                    </div>-->
                <!--                        <div class="deal_detail_offer_with_price d-flex align-items-center">
{{--                            @if($article->converted_original_amount - $article->converted_deal_amount > 0)--}}
                {{--                            <i>--}}
                {{--                                @if($article->discount_type=='percentage')--}}
                {{--                                    {{$article->percentage}}%--}}
                {{--                                @else--}}
                {{--                                {{currencyFormatter($article->converted_original_amount - $article->converted_deal_amount)}}--}}
                {{--                                @endif--}}
                {{--                                @lang('app.off')--}}
                    </i>
{{--                            @endif--}}
                {{--                            <p>{{ $article->formated_deal_amount }}--}}
                {{--                                <span>@if($article->converted_deal_amount < $article->converted_original_amount){{ $article->formated_original_amount }}@endif</span>--}}
                    </p>
                </div>
                <div class="deal_detail_expiry_date">
                    <p>
{{--                                <span>@lang('app.expireOn') : </span>--}}
                {{--                                {{ \Carbon\Carbon::parse($article->end_date_time)->translatedFormat($settings->date_format.', '.$settings->time_format) }}--}}
                    </p>
                </div>
                <div class="form_with_buy_deal d-lg-flex d-md-flex d-block">
{{--                            @if ($article->max_order_per_customer > 1)--}}
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
{{--                                    @if(sizeof($reqProduct) == 0) value="1" @else value="{{$reqProduct['deal'.$article->id]['quantity']}}" @endif--}}
                    min="1"
                    readonly
{{--                                    data-id="{{ $article->id }}"--}}
                {{--                                    data-max-order="{{ $article->max_order_per_customer }}"--}}
                    />
               <div class="value-button" id="increase" value="Increase Value"><i class="zmdi zmdi-plus"></i></div>
           </form>
{{--                            @endif--}}
                {{--                            <div class="add @if(sizeof($reqProduct) == 0) d-flex @else d-none @endif w-100">--}}
                    <button class="btn btn-custom added-to-cart ml-lg-3 ml-md-3 ml-0" id="add-item">
{{--                                     @lang('front.addItem')--}}
                    </button>
                </div>
{{--                            <div class="update mt-2 mt-lg-0 mt-md-0 @if(sizeof($reqProduct) > 0) d-flex @else d-none @endif w-100">--}}
                    <button class="btn btn-custom update-cart ml-lg-3 ml-md-3 ml-0" id="update-item">
{{--                                     @lang('front.buttons.updateCart')--}}
                    </button>
                    <button class="btn btn-custom ml-3 btn-danger" id="delete-product">
{{--                                     @lang('front.table.deleteProduct')--}}
                    </button>
                </div>
            </div>-->
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 deal_detail_content">
                    {!! $article->content !!}
                </div>
            </div>
        </div>
    </section>

    <!-- RELATED ARICLES START -->

    <section class="professionalsSection position-relative" id="professionalsSection">
        <div class="container">
            <div class="heading">
                <p class="mb-0">{{__('menu.professionals')}} {{__('app.in')}} <span id="location_name"></span>
                <span class="float-right">
                    @if(auth()->check())
                        @if(auth()->user()->is_admin)
                            <a href="{{route('admin.toutes.create' , ['id' => $article->id])}}" class="btn btn-link">{{__('app.toutHere')}}</a>
                        @endif
                    @else
                        <a href="{{route('front.register')}}" class="btn btn-link">{{__('app.toutHere')}}</a>
                    @endif
                </span>
                </p>
            </div>
        </div>

        <div class="container" id="paidAdsContainer">

        </div>

        <div class="container" id="professionals">

        </div>
    </section>


    <section class="relatedArticlesSection position-relative" id="relatedArticlesSection">
        <div class="container">
            <div class="heading">
                <p class="mb-0">{{__('menu.relatedArticles')}}</p>
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
    <!-- RELATED ARTICLES END -->


@endsection

@push('footer-script')
    <script>
        loadRelatedArticles();

        /* this function will call on page reload */
        function loadRelatedArticles() {
            var location_id = localStorage.getItem('location');
            var slug = '{{$article->slug}}'
            {{--if ( location_id !== '' && location.href == '{{ route('front.index').'/' }}')--}}
            {{--{--}}
            var url = '{{ route('front.blog.article', ['slug' => ':slug']) }}';
            url = url.replace(':slug', slug);

            $.easyAjax({
                url: url,
                type: 'GET',
                data: {location: location_id},
                blockUI: false,
                success: function (response) {
                    console.log(response);
                    $("#location_name").text(response.location.name)
                    $("#professionals").html(response.view)
                    $("#paidAdsContainer").html(response.viewAds)

                    if (response.articles.length > 0) {
                        var slider_length = $('.spot_box').length;
                        for (var i = 0; i < slider_length; i++) {
                            $("#spotlight_slider").trigger('remove.owl.carousel', [i]).trigger('refresh.owl.carousel');
                        }

                        response.articles.forEach(article => {
                            let detail_url = article.slug;
                            let image_url = article.article_image_url;

                            $('#spotlight_slider').trigger('add.owl.carousel', [jQuery(` <div class="item spot_box">
                                    <div class="spot_box_img">
                                        <a class="ml-auto" href="${detail_url}">
                                            <img src="{{asset('front/images/pixel.gif')}}" data-src="${image_url}" alt="Image" />
                                        </a>
                                    </div>
                                    <div>
                                        <h2 class="title" title="${article.title}">${article.limit_title}</h2>

                                        <p class="">${article.limit_excerpt}</p>
                                    </div>

                                    <p class="px-0">
                                        <div class="col-12 spot_article pt-2">
                                            <a href="${detail_url}" class="w-100">{{__('front.readMore')}}</a>
                                        </div>
                                    </p>
                                </div>`)]).trigger('refresh.owl.carousel');
                        });

                        $('#relatedArticlesSection').show();

                    } else {
                        $('#relatedArticlesSection').hide();
                    }

                    lazyload();
                },
                error: function (error) {
                    console.log(error);
                }/* success closing */
            })
            // }
        }
    </script>
@endpush
