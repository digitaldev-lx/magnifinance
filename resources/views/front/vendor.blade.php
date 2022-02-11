@extends('layouts.front')

@push('styles')
    <link href="{{ asset('front/css/vendor.css') }}" rel="stylesheet">
    <link href=" {{ asset('front/css/all_deals.css') }} " rel="stylesheet">
    <link href="{{ asset('front/css/lightbox.min.css') }}" rel="stylesheet">
    @if ($vendorPage->map_option == 'active' && $settings->map_option == 'active')
        {{-- <link rel="stylesheet" type="text/css"
               href="{{asset('admin-panel/app-assets/css/plugins/images/cropper/cropper.css')}}">--}}
        <link href="{{asset('front/css/croppie.min.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
              integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
              crossorigin=""/>

        <!-- Load Esri Leaflet Geocoder from CDN -->
        <link rel="stylesheet" href="https://unpkg.com/esri-leaflet-geocoder@2.3.3/dist/esri-leaflet-geocoder.css"
              integrity="sha512-IM3Hs+feyi40yZhDH6kV8vQMg4Fh20s9OzInIIAc4nx7aMYMfo+IenRUekoYsHZqGkREUgx0VvlEsgm7nCDW9g=="
              crossorigin="">
    @endif
    <style>
        @if ($vendorPage->default_image)
        /* Banner Image */
        .ven-banner {
            background-image: url('{{ cdn_storage_url($vendorPage->default_image) }}');
        }
        @endif
        #map {
            height: 250px;
            width: 100%;
            /* Google Map Size */
        }

        .select2-container .select2-selection--single {
            height: auto !important;
        }

        .featured_deal_imgBox {
            height: 236px;
            overflow: hidden;
        }
        .all_deals_section {
            width: 100%;
        }
        #filtered_deals {
            width: 100%;
        }

        .select2-container{
            width: 173px !important;
        }
    </style>
@endpush
@section('content')

    <!-- VENDOR BANNER START -->
    <section class="mb-4 ">
        <div class="container moveToTop">
            <div class="row">

                <div class="col-md-8 pr-auto pr-lg-3 pr-md-3 mt-3 mt-lg-0 mt-md-0">
                    @if ($vendorPage->default_image)
                        <a href="{{ cdn_storage_url($vendorPage->default_image) }}"
                            data-lightbox="photos">
                            <div class="ven-banner"></div>
                        </a>
                    @endif
                </div>

                <div class="col-md-4 d-none d-lg-block d-md-block">
                    <div class="row">
                        @if ($vendorPage->photos_without_default_image)

                            @foreach ($vendorPage->photos_without_default_image as $key => $photo)
                                @if ($key > 3) @break
                            @endif
                            @if ($key < 3)
                                <div class="col-md-6 pl-0">
                                    <div class="col-md-12 p-0  mb-3">
                                        <div class="ven-small-banner">
                                            <a href="{{ cdn_storage_url($photo) }}"
                                                data-lightbox="photos">
                                                <img class="img-fluid" src="{{asset('front/images/pixel.gif')}}"
                                                data-src="{{ cdn_storage_url($photo) }}">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6 pl-0">
                                    <div class="ven-small-banner ven-view">
                                        <img src="{{asset('front/images/pixel.gif')}}" data-src="{{ cdn_storage_url($photo) }}"
                                            width="100%" />
                                        <a class="nav-link" id="openGallery" href="javascript:;">@lang('app.viewAll')</a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- VENDOR BANNER END -->

    <!-- VENDOR CONTENT START -->
    <section class="mb-3 ">
        <div class="container">
            <!-- VENDOR HEADING START -->
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mb-0">{{ $company->company_name }}</h2>
                </div>
                <div class="col-md-12">
                    <div class="d-block d-lg-flex d-md-flex justify-content-between align-items-center">
                        <div class="ven-location">
                            <p><i class="zmdi zmdi-pin"></i>&nbsp;&nbsp;{{ $vendorPage->address }}</p>
                        </div>

                        <div class="ven-btn mt-3 mt-lg-0 mt-md-0">
                            @if ($vendorPage->map_option == 'active' && $settings->map_option == 'active')
                                <a href="https://maps.google.com/maps?q={{ ($lat_long['latitude'] ? $lat_long['latitude'] : '26.85259403535702') . ',' . ($lat_long['longitude'] ? $lat_long['longitude'] : '75.80531537532806') }}&z=15"
                                    target="_blank"> <button class="btn btn-custom mr-1"><i
                                            class="zmdi zmdi-turning-sign"></i>&nbsp;&nbsp;@lang('app.direction')
                                    </button></a>
                            @endif
                            <button type="button" class="btn btn-custom" onclick="setClipboard('{{route('front.vendorPage', $company->slug)}}')"><i class="zmdi zmdi-share" ></i>&nbsp;&nbsp;@lang('app.share')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- VENDOR HEADING END -->
            <!-- VENDOR TAB START -->
            <div class="mt-4 ven-tab ">
                <ul class="nav nav-tabs " id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab"
                            aria-controls="overview" aria-selected="true">@lang('app.overview')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="book-services-tab" data-toggle="tab" href="#book-services" role="tab"
                            aria-controls="book-services" aria-selected="false">@lang('app.book') @lang('app.services')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="buy-deals-tab" data-toggle="tab" href="#buy-deals" role="tab"
                            aria-controls="buy-deals" aria-selected="false">@lang('app.buy') @lang('menu.deals')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="gallery-tab" data-toggle="tab" href="#gallery" role="tab"
                            aria-controls="gallery" aria-selected="false">@lang('app.gallery')</a>
                    </li>
                </ul>
                <div class="tab-content py-4" id="myTabContent">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="">
                                    @if ($vendorPage->description)
                                        <h6 class="font-weight-500">@lang('app.description')</h6>
                                        <p class="text-justify f-14">
                                            {!! $vendorPage->description !!}
                                        </p>
                                    @endif
                                </div>
                                @if ($vendorPage->map_option == 'active' && $settings->map_option == 'active')
                                    <div class="mt-4">
                                        <h6 class="font-weight-500">@lang('app.directions')</h6>
                                        <div class="mt-3" id="map"></div>
                                    </div>
                                @endif

                            </div>
                            <div class="col-md-4">
                                <div class="ven-call-time">
                                    @if ($vendorPage->primary_contact || $vendorPage->secondary_contact)
                                        <h6 class="font-weight-500">@lang('app.call')</h6>
                                        <div class="mt-3 d-flex flex-column f-14">
                                            @if ($vendorPage->primary_contact)<a
                                                    href="tel:{{ $vendorPage->primary_contact }}" class="mb-1"><i
                                                        class="zmdi zmdi-phone"></i>&nbsp;&nbsp;
                                                    {{ $vendorPage->primary_contact }}</a>@endif
                                            @if ($vendorPage->secondary_contact) <a
                                                    href="tel:{{ $vendorPage->secondary_contact }}" class="mb-1"><i
                                                        class="zmdi zmdi-phone"></i>&nbsp;&nbsp;
                                                    {{ $vendorPage->secondary_contact }}</a> @endif
                                        </div>
                                    @endif

                                    <h6
                                        class="font-weight-500 {{ $vendorPage->primary_contact || $vendorPage->secondary_contact ? 'mt-4' : 'mt-2' }} ">
                                        @lang('app.timings')</h6>
                                    <div class="mt-3 d-flex flex-column">
                                        <ul class="list-items f-14 pl-0 mb-0">
                                            @forelse ($bookingTimes as $bookingTime)
                                                <li class="d-flex justify-content-between mb-1">
                                                    {{ ucfirst($bookingTime->day) }} <span class="">
                                                        {{ $bookingTime->status == 'enabled' ? strtoupper($bookingTime->start_time) . ' - ' . strtoupper($bookingTime->end_time) : __('app.close') }}
                                                    </span> </li>
                                            @empty
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="book-services" role="tabpanel" aria-labelledby="book-services-tab">
                        <div class="row">
                            <!-- ALL SERVICES START -->
                            <section class="all_deals_section">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-md-4 d-none d-lg-block">
                                            <!-- FILTER START -->
                                            <div class="filter_heading d-flex justify-content-between">
                                                <h2 class="mb-0">@lang('app.filter')</h2>
                                                <div class="clear_all_filter d-none">
                                                    <div class="clear_effect"></div>
                                                    <a class="d-flex align-items-center justify-content-center clearAll"
                                                        href="{{ route('front.vendorPage', $company->slug) }}"><i
                                                            class="zmdi zmdi-close"></i> @lang('front.clearAll')</a>
                                                </div>
                                            </div>
                                            <div class="filter_types">

                                                <div class="card">
                                                    <div class="card-header">
                                                        <button class="card-link" type="button" data-toggle="collapse"
                                                            data-target=".category"
                                                            aria-expanded="true">@lang('front.categories')</button>
                                                    </div>
                                                    <div id="category" class="category collapse show">
                                                        <div class="card-body">

                                                            @foreach ($categories as $category)
                                                                @if ($loop->iteration < 6)
                                                                    <input id='{{ $category->slug }}' type='checkbox'
                                                                        name="categories[]" value="{{ $category->id }}"
                                                                        class="categories apply-filter" />
                                                                    <label for='{{ $category->slug }}' class="mb-3">
                                                                        <span></span>{{ $category->name }}
                                                                    </label><!-- SINGLE CATEGORY END -->
                                                                @endif
                                                            @endforeach

                                                            <span id="category_span"></span>

                                                            <span id="more_category">

                                                                @foreach ($categories as $category)
                                                                    @if ($loop->iteration >= 6)
                                                                        <input id='{{ $category->slug }}' type='checkbox'
                                                                            name="categories[]"
                                                                            value="{{ $category->id }}"
                                                                            class="categories apply-filter" />
                                                                        <label for='{{ $category->slug }}' class="mb-3">
                                                                            <span></span>{{ $category->name }}
                                                                        </label><!-- SINGLE CATEGORY END -->
                                                                    @endif
                                                                @endforeach

                                                            </span>

                                                            @if ($categories->count() >= 6)
                                                                <button id="view_all_categories"
                                                                    class="view_all_categories">@lang('app.viewAll')</button>
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- CATEGORY CARD END -->

                                                <div class="card">
                                                    <div class="card-header">
                                                        <button class="card-link" type="button" data-toggle="collapse"
                                                            data-target="#price"
                                                            aria-expanded="true">@lang('app.price')</button>
                                                    </div>
                                                    <div id="price" class="collapse show">
                                                        <div class="card-body">
                                                            <input id='price_option1' type='checkbox' name="prices[]"
                                                                value="1-99" class="prices" />
                                                            <label for='price_option1' class="mb-3">
                                                                <span></span>1 - 99
                                                            </label>

                                                            <input id='price_option2' type='checkbox' name="prices[]"
                                                                value="100-499" class="prices" />
                                                            <label for='price_option2' class="mb-3">
                                                                <span></span>100 - 499
                                                            </label>

                                                            <input id='price_option3' type='checkbox' name="prices[]"
                                                                value="500-999" class="prices" />
                                                            <label for='price_option3' class="mb-3">
                                                                <span></span>500 - 999
                                                            </label>

                                                            <input id='price_option4' type='checkbox' name="prices[]"
                                                                value="1000-2999" class="prices" />
                                                            <label for='price_option4' class="mb-3">
                                                                <span></span>1000 - 2999
                                                            </label>

                                                            <input id='price_option5' type='checkbox' name="prices[]"
                                                                value="3000-4999" class="prices" />
                                                            <label for='price_option5' class="mb-3">
                                                                <span></span>3000 - 4999
                                                            </label>

                                                            <input id='price_option6' type='checkbox' name="prices[]"
                                                                value="5000-More" class="prices" />
                                                            <label for='price_option6' class="mb-3">
                                                                <span></span>5000 - More
                                                            </label>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- PRICE CARD END -->

                                                <div class="card">
                                                    <div class="card-header">
                                                        <button class="card-link" type="button" data-toggle="collapse"
                                                            data-target="#discount"
                                                            aria-expanded="true">@lang('app.discount')</button>
                                                    </div>
                                                    <div id="discount" class="collapse show">
                                                        <div class="card-body">
                                                            <input id='10p' type='checkbox' name="discount[]" value="10-24"
                                                                class="discounts" />
                                                            <label for='10p' class="mb-3">
                                                                <span></span>10% @lang('modules.filter.flatDiscount')
                                                            </label>

                                                            <input id='25p' type='checkbox' name="discount[]" value="25-34"
                                                                class="discounts" />
                                                            <label for='25p' class="mb-3">
                                                                <span></span>25% @lang('modules.filter.flatDiscount')
                                                            </label>

                                                            <input id='35p' type='checkbox' name="discount[]" value="35-49"
                                                                class="discounts" />
                                                            <label for='35p' class="mb-3">
                                                                <span></span>35% @lang('modules.filter.flatDiscount')
                                                            </label>

                                                            <input id='50p' type='checkbox' name="discount[]" value="50-100"
                                                                class="discounts" />
                                                            <label for='50p' class="mb-3">
                                                                <span></span>50% @lang('modules.filter.flatDiscount')
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div><!-- DISCOUNT CARD END -->

                                            </div>
                                            <!-- FILTER END -->
                                        </div>
                                        <div class="col-lg-8">
                                            <!-- DEAL START -->
                                            <div class="sort_box d-lg-flex d-block d-md-flex justify-content-between">
                                                <div class="col-md-6 d-flex align-items-center mobile-no-padding">
                                                    <p id="filtered_deals_count"> </p>
                                                </div>
                                                <div
                                                    class="col-md-6 d-flex justify-content-end position-relative mobile-no-padding">
                                                    <div class="input-group">
                                                        <div class="input-group-append location_icon">
                                                            <span class="input-group-text">@lang('front.sortBy'):</span>
                                                        </div>
                                                        <select class="myselect apply-filter" name="sort_by" id="sort_by">
                                                            <option value="">@lang('app.choose')</option>
                                                            <option value="newest">@lang('front.newest')</option>

                                                            <option value="low_to_high">@lang('app.price')
                                                                (@lang('front.lowToHigh'))</option>
                                                            <option value="high_to_low">@lang('app.price')
                                                                (@lang('front.highToLow'))</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="filtered_services">
                                                <div class="mt-4  d-flex flex-wrap">
                                                    {{-- Placeholder that will show until data didn't load --}}
                                                    @for ($i = 0; $i < 8; $i++)
                                                    <div class="col-md-6 mobile-no-padding">
                                                        <div class="card single_deal_box border-0">
                                                            <div class="ph-item">
                                                                <div class="ph-col-12 ph-card-image"></div>
                                                                <div class="py-2 pl-2 pr-2 mt-2">
                                                                    <div class="ph-row">
                                                                        <div class="ph-col-12 big"></div>
                                                                        <div class="ph-col-12 big"></div>
                                                                        <div class="ph-col-12"></div>
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
                                                    </div>
                                                    @endfor

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
                                            <div class="clear_all_filter d-none">
                                                <div class="clear_effect"></div>
                                                <a class="d-flex align-items-center justify-content-center"
                                                    href="{{ route('front.vendorPage', $company->slug) }}"><i
                                                        class="zmdi zmdi-close"></i> @lang('front.clearAll')</a>
                                            </div>
                                        </div>
                                        <div class="filter_types">
                                            <div class="card">
                                                <div class="card-header">
                                                    <button class="card-link" type="button" data-toggle="collapse"
                                                        data-target=".category"
                                                        aria-expanded="true">@lang('front.categories')</button>
                                                </div>
                                                <div id="category" class="category collapse show">
                                                    <div class="card-body">

                                                        @foreach ($categories as $category)
                                                            <input id='{{ $category->slug }}{{ $category->id }}'
                                                                type='checkbox' name="categories[]"
                                                                value="{{ $category->id }}"
                                                                class="categories apply-filter" />
                                                            <label for='{{ $category->slug }}{{ $category->id }}'
                                                                class="mb-3">
                                                                <span></span>{{ $category->name }}
                                                            </label>
                                                        @endforeach

                                                        <span id="category_mbl_span"></span>

                                                        <span id="more_mbl_category">
                                                            <input id='hotels_mbl' type='checkbox' />
                                                            <label for='hotels_mbl' class="mb-3">
                                                                <span></span>@lang('modules.filter.hotels')
                                                            </label>
                                                        </span>

                                                        @if ($categories->count() >= 6)
                                                            <button id="view_all_mbl_categories"
                                                                class="d-block view_all_mbl_categories">@lang('app.viewAll')</button>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- CATEGORY CARD END -->

                                            <div class="card">
                                                <div class="card-header">
                                                    <button class="card-link" type="button" data-toggle="collapse"
                                                        data-target="#price"
                                                        aria-expanded="true">@lang('app.price')</button>
                                                </div>
                                                <div id="price" class="collapse show">
                                                    <div class="card-body">
                                                        <input id='price_option1_m' type='checkbox' name="prices[]"
                                                            value="1-99" class="prices" />
                                                        <label for='price_option1_m' class="mb-3">
                                                            <span></span>1 - 99
                                                        </label><!-- SINGLE CATEGORY END -->

                                                        <input id='price_option2_m' type='checkbox' name="prices[]"
                                                            value="100-499" class="prices" />
                                                        <label for='price_option2_m' class="mb-3">
                                                            <span></span>100 - 499
                                                        </label><!-- SINGLE CATEGORY END -->

                                                        <input id='price_option3_m' type='checkbox' name="prices[]"
                                                            value="500-999" class="prices" />
                                                        <label for='price_option3_m' class="mb-3">
                                                            <span></span>500 - 999
                                                        </label><!-- SINGLE CATEGORY END -->

                                                        <input id='price_option4_m' type='checkbox' name="prices[]"
                                                            value="1000-2999" class="prices" />
                                                        <label for='price_option4_m' class="mb-3">
                                                            <span></span>1000 - 2999
                                                        </label><!-- SINGLE CATEGORY END -->

                                                        <input id='price_option5_m' type='checkbox' name="prices[]"
                                                            value="3000-4999" class="prices" />
                                                        <label for='price_option5_m' class="mb-3">
                                                            <span></span>3000 - 4999
                                                        </label><!-- SINGLE CATEGORY END -->


                                                        <input id='price_option6_m' type='checkbox' name="prices[]"
                                                            value="5000-More" class="prices" />
                                                        <label for='price_option6_m' class="mb-3">
                                                            <span></span>5000 - @lang('modules.filter.more')
                                                        </label><!-- SINGLE CATEGORY END -->

                                                    </div>
                                                </div>
                                            </div><!-- PRICE CARD END -->

                                            <div class="card">
                                                <div class="card-header">
                                                    <button class="card-link" type="button" data-toggle="collapse"
                                                        data-target="#discount"
                                                        aria-expanded="true">@lang('app.discount')</button>
                                                </div>
                                                <div id="discount" class="collapse show">
                                                    <div class="card-body">
                                                        <input id='10p_m' type='checkbox' name="discount[]" value="10-24"
                                                            class="discounts" />
                                                        <label for='10p_m' class="mb-3">
                                                            <span></span>10% @lang('modules.filter.flatDiscount')
                                                        </label><!-- SINGLE CATEGORY END -->

                                                        <input id='25p_m' type='checkbox' name="discount[]" value="25-34"
                                                            class="discounts" />
                                                        <label for='25p_m' class="mb-3">
                                                            <span></span>25% @lang('modules.filter.flatDiscount')
                                                        </label><!-- SINGLE CATEGORY END -->

                                                        <input id='35p_m' type='checkbox' name="discount[]" value="35-49"
                                                            class="discounts" />
                                                        <label for='35p_m' class="mb-3">
                                                            <span></span>35% @lang('modules.filter.flatDiscount')
                                                        </label><!-- SINGLE CATEGORY END -->

                                                        <input id='50p_m' type='checkbox' name="discount[]" value="50-100"
                                                            class="discounts" />
                                                        <label for='50p_m' class="mb-3">
                                                            <span></span>50% @lang('modules.filter.flatDiscount')
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
                                <div id="filter_modal" class="filter_modal_wrapper">@lang('app.filter')&nbsp;&nbsp;<i
                                        class="zmdi zmdi-filter-list"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="buy-deals" role="tabpanel" aria-labelledby="buy-deals-tab">
                        <div class="row">
                            <div id="filtered_deals">
                                <div class="mt-4 d-flex flex-wrap">
                                    {{-- Placeholder that will show until data didn't load --}}
                                    @for ($i = 0; $i < 8; $i++)
                                    <div class="col-md-6 mt-3">
                                        <div class="ph-item">
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
                                    </div>
                                    @endfor

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="gallery" role="tabpanel" aria-labelledby="gallery-tab">
                        <div class="row photos">
                            @if ($vendorPage->photos)
                                @foreach ($vendorPage->photos as $photo)
                                    <div class="col-sm-6 col-md-4 col-lg-2 item">
                                        <a href="{{ cdn_storage_url($photo) }}"
                                            data-lightbox="photos">
                                            <img class="img-fluid" src="{{asset('front/images/pixel.gif')}}"
                                            data-src="{{ cdn_storage_url($photo) }}">
                                        </a>
                                    </div>
                                @endforeach
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            <!-- VENDOR TAB END -->
        </div>
    </section>
    <!-- VENDOR CONTENT END -->
@endsection

@push('footer-script')
@if ($vendorPage->map_option == 'active' && $settings->map_option == 'active')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>

    <!-- Load Esri Leaflet from CDN -->
    <script src="https://unpkg.com/esri-leaflet@2.5.3/dist/esri-leaflet.js"
            integrity="sha512-K0Vddb4QdnVOAuPJBHkgrua+/A9Moyv8AQEWi0xndQ+fqbRfAFd47z4A9u1AW/spLO0gEaiE1z98PK1gl5mC5Q=="
            crossorigin=""></script>


    <script src="https://unpkg.com/esri-leaflet-geocoder@2.3.3/dist/esri-leaflet-geocoder.js"
            integrity="sha512-HrFUyCEtIpxZloTgEKKMq4RFYhxjJkCiF5sDxuAokklOeZ68U2NPfh4MFtyIVWlsKtVbK5GD2/JzFyAfvT5ejA=="
            crossorigin=""></script>

@endif


<script src="{{asset('front/js/lightbox.min.js')}}"></script>
    <script>
        @if ($vendorPage->map_option == 'active' && $settings->map_option == 'active')
            initMap()
        @endif

        function setClipboard(value) {
            var tempInput = document.createElement("input");
            tempInput.style = "position: absolute; left: -1000px; top: -1000px";
            tempInput.value = value;
            document.body.appendChild(tempInput);
            tempInput.select();
            var copy = document.execCommand("copy");
            document.body.removeChild(tempInput);
            if(copy){
                toastr.success('@lang("app.copyMesssage")')
            }
        }

        $(function () {


            $('body').on('click', '#myTab a', function (e) {
                e.preventDefault();
                $(this).tab('show');
                // $("html, body").scrollTop(0);
            });

            // store the currently selected tab in the hash value
            $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
                e.preventDefault();
                var id = $(e.target).attr("href").substr(1);
                var elem = $('#'+id);
                var newID= id+'-tmp';
                elem.attr('id',newID);
                window.location.hash = id;
                elem.attr('id',id);
                fakeScroll();
            });

            // on load of the page: switch to the currently selected tab
            var hash = window.location.hash;
            $('#myTab a[href="' + hash + '"]').tab('show');
        });

            $('body').on('click', '#openGallery', function (e) {
                e.preventDefault();
                $('a[href="#gallery"]').tab('show');
            });
        function fakeScroll() {
            var currentLocation = $(window).scrollTop();
            $("html, body").animate({ scrollTop: currentLocation + 1}, 1);
            $("html, body").animate({ scrollTop: currentLocation}, 1);
        }

        $('body').on('click change', '.apply-filter', function(e) {
            filter();
        });

        var productsCount = '{{ $productsCount }}';

        $('body').on('change', '#location', function(e) {
            e.preventDefault();
            localStorage.setItem('location', $(this).val());
            filter();
        });

        $('body').on('click', '.prices', function() {
            var $box = $(this);
            if ($box.is(":checked")) {
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
            }
            removeClearAll();
            filter();
        });

        $('body').on('click', '.discounts', function() {
            var $box = $(this);
            if ($box.is(":checked")) {
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
            }
            removeClearAll();
            filter();
        });

        function removeClearAll() {
            var categories = [];
            $.each($("input[name='categories[]']:checked"), function(){
                categories.push($(this).val());
            });

            var locations = [];
            if(localStorage.getItem('location')) {
                locations.push(localStorage.getItem('location'));
            }

            var price = [];
            $.each($("input[name='prices[]']:checked"), function(){
                price.push($(this).val());
            });

            var companies = [];
            $.each($("input[name='companies[]']:checked"), function(){
                companies.push($(this).val());
            });

            var discounts = [];
            $.each($("input[name='discount[]']:checked"), function(){
                discounts.push($(this).val());
            });

            if(categories == '' && price == '' && companies == '' && discounts == ''){
                $(".clear_all_filter").hide()
                return false;
            }else{
                $(".clear_all_filter").show()
                return false;
            }
        }

        function filter() {
            var queryString = window.location.search;

            var urlParams = new URLSearchParams(queryString);
            var service_name = urlParams.get('q');
            var company_id = {{$company->id}};
            var term = urlParams.get('term');

            term ? $('#search-term').html('@lang('front.resultsFor') "'+term+'"') : $('#search-term').html('@lang('front.allServices')');

            var categories = [];
            $.each($("input[name='categories[]']:checked"), function(){
                categories.push($(this).val());
            });

            var locations = [];
            if(localStorage.getItem('location')) {
                locations.push(localStorage.getItem('location'));
            }

            var price = [];
            $.each($("input[name='prices[]']:checked"), function(){
                price.push($(this).val());
            });

            var companies = [];
            $.each($("input[name='companies[]']:checked"), function(){
                companies.push($(this).val());
            });

            var discounts = [];
            $.each($("input[name='discount[]']:checked"), function(){
                discounts.push($(this).val());
            });

            if(categories.length == 0 && price.length == 0 && companies.length == 0 && discounts.length == 0){
                $(".clear_all_filter").addClass('d-none');
            }else{
                service_name = '';
                company_id = '';
                $(".clear_all_filter").removeClass('d-none');
            }

            $.easyAjax({
                url: '{{ route('front.services', "all") }}',
                type: 'GET',
                blockUI: false,
                container: '#filtered_services',
                data: {categories : categories.join(",") , locations : locations.join(",") , price : price , companies : companies.join(","), discounts : discounts, sort_by : $('#sort_by').val(), service_name : service_name, company_id : company_id, term : term},
                success: function (response) {
                    if(response.service_count==0) {
                        let image_path = '{{ asset("front/images/no-search-result.png") }}';
                        $('#filtered_services').html('<div class="mt-4 no_result text-center noResultFound"><img src="{{asset('front/images/pixel.gif')}}" data-src="'+image_path+'" class="mx-auto d-block" alt="Image" width="40%" /><h2 class="mt-3">@lang("messages.noResultFound") :(</h2><p>@lang("messages.checkSpellingOrUseGeneralTerms")</p></div>');
                    } else {
                        $('#filtered_services').html(response.view);
                    }
                    $('#filtered_deals_count').html('@lang("app.showing") '+response.service_count+' @lang("app.of") '+response.service_total+' @lang("app.results")');
                    lazyload();
                }
            })
        }

        $('body').on('click', '#pagination a', function(e) {
            e.preventDefault();

            var page = $(this).attr('href').split('page=')[1];

            var categories = [];
            $.each($("input[name='categories[]']:checked"), function(){
                categories.push($(this).val());
            });

            var locations = [];
            if(localStorage.getItem('location')) {
                locations.push(localStorage.getItem('location'));
            }

            var price = [];
            $.each($("input[name='prices[]']:checked"), function(){
                price.push($(this).val());
            });

            var companies = [];
            $.each($("input[name='companies[]']:checked"), function(){
                companies.push($(this).val());
            });

            var discounts = [];
            $.each($("input[name='discount[]']:checked"), function(){
                discounts.push($(this).val());
            });

            var url = '{{ route("front.services", "all") }}?page='+page+'&categories='+categories+'&locations='+locations+'&price='+price+'&companies='+companies+'&discounts='+discounts+'&sort_by='+$('#sort_by').val();
            $.get(url, function(response){
                $('#filtered_services').html(response.view);
                $('#filtered_deals_count').html('@lang("app.showing") '+response.service_count+' @lang("app.of") '+response.service_total+' @lang("app.results")');
                lazyload();
            });

            moveToTop();
        });

        function moveToTop() {
            $('html, body').animate({
                scrollTop: $(".moveToTop").offset().top
            }, 1000);
        }


        // Company Deals
        $.easyAjax({
                url: '{{ route('front.companyDeals',$company->slug) }}',
                type: 'GET',
                blockUI: false,
                container: '#filtered_deals',
                success: function (response) {
                    if(response.deal_count!=0)
                    {
                        $('#filtered_deals').html(response.view);
                    }
                    else
                    {
                        let image_path = '{{ asset("front/images/no-search-result.png") }}';
                        $('#filtered_deals').html('<div class="mt-4 no_result text-center noResultFound"><img src="{{asset('front/images/pixel.gif')}}" data-src="'+image_path+'" class="mx-auto d-block" alt="Image" width="40%" /><h2 class="mt-3">@lang("messages.noResultFound") :(</h2><p>@lang("messages.checkSpellingOrUseGeneralTerms")</p></div>');
                    }
                    lazyload();
                }
            })

        $('body').on('click', '#filtered_deals #pagination a', function(e){
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            var url = '{{ route("front.companyDeals",$company->slug) }}?page='+page;
            $.get(url, function(response){
                $('#filtered_deals').html(response.view);
                lazyload();
            });

            moveToTop();
        });

        // Initialize and add the map
        function initMap() {
            var map = L.map('map').setView([{{$lat_long['latitude']}}, {{$lat_long['longitude']}}], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
            }).addTo(map);
            var marker = L.marker([{{$lat_long['latitude']}}, {{$lat_long['longitude']}}], {
                draggable: 'false',
            }).addTo(map);
        }
    </script>
@endpush
