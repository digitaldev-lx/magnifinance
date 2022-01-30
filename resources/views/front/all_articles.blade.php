@extends('layouts.front')

@push('styles')
    <link href="{{ asset('front/css/all_articles.css') }}" rel="stylesheet">
    <style>
        .select2-container .select2-selection--single {
            height: auto !important;
        }

    </style>
@endpush

@section('content')

    <!-- BREADCRUMB START -->
    <section class="breadcrumb_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 col-md-5">
                    <h1 class="mb-0">{{__('front.articles')}}</h1>
                </div>
                <div class="col-lg-5 col-md-7">
                    <nav>
                        <ol class="breadcrumb mb-0 justify-content-center">
                            <li class="breadcrumb-item"><a href="/">@lang('app.home')</a></li>
                            <li class="breadcrumb-item active"><span>@lang('front.articles')</span></li>
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
            <div class="row moveToTop">
                <div class="col-md-4 d-none d-lg-block">
                    <!-- FILTER START -->
                    <div class="filter_heading d-flex justify-content-between">
                        <h2 class="mb-0">@lang('app.filter')</h2>
                        <div class="clear_all_filter d-none">
                            <div class="clear_effect"></div>
                            <a class="d-flex align-items-center justify-content-center" href=""><i class="zmdi zmdi-close"></i> @lang('front.clearAll')</a>
                        </div>
                    </div>
                    <div class="filter_types">

                            <div class="card">
                                <div class="card-header">
                                    <button class="card-link" type="button" data-toggle="collapse" data-target="#category" aria-expanded="true">@lang('front.categories')</button>
                                </div>
                                <div id="category" class="collapse show">
                                    <div class="card-body">

                                        @foreach ($categories as $category)
                                            @if ($loop->iteration<6)
                                            <input id='{{$category->slug}}' type='checkbox' name="categories[]" value="{{$category->id}}" class="categories apply-filter"/>
                                            <label for='{{$category->slug}}' class="mb-3">
                                                <span></span>{{$category->name}}
                                            </label>
                                            @endif
                                        @endforeach

                                        <span id="category_span"></span>

                                        <span id="more_category">
                                            @foreach ($categories as $category)
                                                @if ($loop->iteration>=6)
                                                <input id='{{$category->slug}}' type='checkbox' name="categories[]" value="{{$category->id}}" class="categories apply-filter" />
                                                <label for='{{$category->slug}}' class="mb-3">
                                                    <span></span>{{$category->name}}
                                                </label>
                                                @endif
                                            @endforeach
                                        </span>

                                        @if ($categories->count()>=6)
                                            <button id="view_all_categories" class="view_all_categories">@lang('app.viewAll')</button>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <!-- CATEGORY CARD END -->

                    </div>
                    <!-- FILTER END -->
                </div>
                <div class="col-lg-8 ">
                    <!-- DEAL START -->

                    <div id="filtered_deals">

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
                        <a class="d-flex align-items-center justify-content-center" href=""><i class="zmdi zmdi-close"></i> @lang('front.clearAll')</a>
                    </div>
                </div>
                <div class="filter_types">

                    <div class="card">
                        <div class="card-header">
                            <button class="card-link" type="button" data-toggle="collapse" data-target="#category" aria-expanded="true">@lang('front.categories')</button>
                        </div>
                        <div id="category" class="collapse show">
                            <div class="card-body">

                                @foreach ($categories as $category)
                                    @if ($loop->iteration<6)
                                        <input id='{{$category->slug}}{{$category->id}}' type='checkbox' name="categories[]" value="{{$category->id}}" class="categories apply-filter"/>
                                        <label for='{{$category->slug}}{{$category->id}}' class="mb-3">
                                            <span></span>{{$category->name}}
                                        </label><!-- SINGLE CATEGORY END -->
                                    @endif
                                @endforeach

                                <span id="category_mbl_span"></span>

                                <span id="more_mbl_category">
                                    @foreach ($categories as $category)
                                        @if ($loop->iteration>=6)
                                            <input id='{{$category->slug}}{{$category->id}}' type='checkbox' name="categories[]" value="{{$category->id}}" class="categories apply-filter" />
                                            <label for='{{$category->slug}}{{$category->id}}' class="mb-3">
                                                <span></span>{{$category->name}}
                                            </label><!-- SINGLE CATEGORY END -->
                                        @endif
                                    @endforeach
                                </span>

                                @if ($categories->count() >= 6)
                                    <button id="view_all_mbl_categories" class="d-block view_all_mbl_categories">@lang('app.viewAll')</button>
                                @endif

                            </div>
                        </div>
                    </div>
                    <!-- CATEGORY CARD END -->

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
        filter();
        $('body').on('click change', '.apply-filter', function(e) {
            filter();
        });

        function filter() {
            var categories = [];
            $.each($("input[name='categories[]']:checked"), function(){
                categories.push($(this).val());
            });

            var companies = [];
            $.each($("input[name='companies[]']:checked"), function(){
                companies.push($(this).val());
            });

            if(categories.length == 0 && companies.length == 0){
                $(".clear_all_filter").addClass('d-none');
            }else{
                $(".clear_all_filter").removeClass('d-none');
            }

            $.easyAjax({
                url: '{{ route('front.blog') }}',
                type: 'GET',
                blockUI: false,
                container: '#filtered_deals',
                data: {categories : categories.join(",") , companies : companies.join(",")},
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
                    $('#filtered_deals_count').html('@lang("app.showing") '+response.deal_count+' @lang("app.of") '+response.deal_total+' @lang("app.results")');
                    lazyload();
                },
                error: function (error){
                    console.log(error);
                }
            })
        }

        $('body').on('click', '#pagination a', function(e){
            e.preventDefault();

            var page = $(this).attr('href').split('page=')[1];

            var categories = [];
            $.each($("input[name='categories[]']:checked"), function(){
                categories.push($(this).val());
            });

            var companies = [];
            $.each($("input[name='companies[]']:checked"), function(){
                companies.push($(this).val());
            });

            var url = '{{ route("front.blog") }}?page='+page+'&categories='+categories+'&companies='+companies;
            $.get(url, function(response){
                $('#filtered_deals').html(response.view);
                $('#filtered_deals_count').html('Showing '+response.deal_count+' of '+response.deal_total+' results');
                lazyload();
            });

            moveToTop();
        });

        function moveToTop() {
            $('html, body').animate({
                scrollTop: $(".moveToTop").offset().top
            }, 2000);
        }

    </script>
@endpush

