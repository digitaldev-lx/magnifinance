<style>
    .dropdown-menu .nav-item{
        padding: 5px 25px 5px 20px;
    }
    .dropdown-menu .row{
        max-height: calc(100vh - 200px);
        height: 100%;
        overflow-y: auto;
    }
    .navbar .dropdown-menu {
        box-shadow: 0 3px 15px 0 rgba(0, 0, 0, 0.1);
    }
    .row::-webkit-scrollbar {
    width: 5px;
    background: #ccc;
    height: 50px;
    }
    .row::-webkit-scrollbar-thumb {
    border-radius: 20px;
    background-color: black ;
    }
    .row::-moz-selection {
    background: #222;
    color: white;
    }
    .row::selection {
    background: #222;
    color: white;
    }
    .headerBottom .dropdown .dropdown-menu ul li a {
        font-size: 13px;
        font-weight: normal;
        line-height: 2;
        color: #212529 !important;
        transition: 1s ease;
        padding: 0px !important;
    }
    .loading {
        padding-left: 0px !important;
    }
    .dif {
        display: inline-flex;
    }
    .p-10 {
        padding: 10px;
    }
    .dim {
        height:50px;
        width:50px;
    }
</style>

<!-- HEADER START -->
<header>
    <div class="headerTop">
        <div class="container">
            <div class="row ">
                <div class="col-lg-8 col-md-7 d-flex headerTopLeft mb-0 pb-0">
                    <a href="{{ route('front.index') }}" class="logo-image"><img src="{{ $frontThemeSettings->logo_url }}" alt="Logo" width="auto"/></a>

                    @if (Route::current()->uri!=='register')
                        <div class="input-group justify-content-end hide_mobile">
                            <div class="input-group-append location_icon d-none d-lg-flex">
                                <span class="input-group-text"><i class="zmdi zmdi-pin"></i></span>
                            </div>
                            <select class="myselect" id="location" name="location">
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                            </select>

                            <form id="searchForm" action="{{ route('front.search') }}" method="GET" class="dif">
                                <input type="text" placeholder="@lang('front.frontSearch')..." name="term" id="globalSearch" class=" form-control globalSearch">

                                <div class="input-group-append search_icon">
                                    <button type="submit" class="btn input-group-text submit"><i class="zmdi zmdi-hc-lg zmdi-search"></i></button>
                                </div>
                            </form>
                        </div>
                        <div class="mob_login_nav_box hide_desktop">
                            <a href="{{ route('front.cartPage') }}" class="mob_login"><i class="zmdi zmdi-shopping-cart"></i><span class="cart-badge cart-badge">{{ $productsCount }}</span></a>
                            <a href="{{ route('login') }}" class="mob_login"><i class="zmdi zmdi-account-o"></i></a>
                            <span class="open-nav"><i class="zmdi zmdi-hc-lg zmdi-menu"></i></span>
                        </div>
                    @endif

                </div>

                <div class="col-lg-4 col-md-5 d-flex headerTopRight hide_mobile">
                    <select class="myselect align-items-center" id="language">
                        @foreach ($languages as $language)
                            <option @if (\Cookie::get('localstorage_language_code')==$language->language_code) selected @endif value="{{ $language->language_code }}">{{ $language->language_name }}</option>
                        @endforeach
                    </select>


                    <div class="company_account_detail">

                      @if($user)
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                          @csrf
                          <div class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                {{ $user->name }}
                              </a>
                              <div class="dropdown-menu bg-white">
                                @if ($user->hasRole('superadmin') || $user->hasRole('agent'))
                                  <a class="" href="{{ route('superadmin.dashboard') }}">
                                @else
                                  <a class="" href="{{ route('admin.dashboard') }}">
                                @endif
                                      <i class="fa fa-user mr-2"></i> @lang('front.myAccount')</a>
                                <a class="front-logout" href="javascript:;">
                                      <i class="fa fa-sign-out mr-2"> </i>@lang('app.logout')</a>
                              </div>
                          </div>
                        </form>
                      @else
                          <a href="{{ route('login') }}" class="header_log_reg"><i class="zmdi zmdi-account-o"></i>@lang('app.signIn') </a>
                      @endif

                    </div>

                </div>
                <div class="col-md-12 mb-3 mobSearch hide_desktop input-group">
                    <div class="input-group">

                        <form id="searchFormMobile" action="{{ route('front.search') }}" method="GET" class="w-100 dif">
                            <input type="text" placeholder="@lang('front.frontSearch')..." name="term" id="globalSearchMobile" class=" form-control globalSearch">

                            <div class="input-group-append search_icon">
                                <button type="submit" class="input-group-text submit"><i class="zmdi zmdi-hc-lg zmdi-search text-white"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-6 mb-3 mobSearch hide_desktop input-group">
                    <select class="myselect align-items-center" id="language_mobile">
                        @foreach ($languages as $language)
                            <option @if (\Cookie::get('localstorage_language_code')==$language->language_code) selected @endif value="{{ $language->language_code }}">{{ $language->language_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 mb-3 mobSearch hide_desktop input-group">
                    <div class="input-group justify-content-end">
                        <div class="input-group-append location_icon d-none d-lg-flex">
                            <span class="input-group-text"><i class="zmdi zmdi-pin"></i></span>
                        </div>
                        <select class="myselect" id="location_mobile" name="location">
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>

                    </div>
                </div>



            </div>

        </div>
    </div>

    @if (Route::current()->uri!=='register')

    <div class="headerBottom">
        <div class="container">
            <!-- DESKTOP NAVBAR START -->
            <nav class="d-none d-md-block py-0 navbar navbar-expand-lg">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav categories_main_menu mr-auto">
                        <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="" data-toggle="dropdown"  aria-expanded="false">
                                  @lang('app.categories')
                                </a>
                                <div class="dropdown-menu bg-white rounded">
                                  <div class="container">
                                    <div class="row">
                                        <ul class="nav flex-column">
                                            @foreach ($headerCategories as $category)
                                                <li class="nav-item">
                                                <a class="nav-link active" href="/{{$category->slug}}/services"><span></span>{{ $category->name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                  </div>
                                </div>
                            </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('front.deals') }}">@lang('menu.deals')</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('front.blog') }}">{{__('menu.blog')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('front.page', 'contact-us') }}"> @lang('app.contactUs') </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('front.register') }}"> @lang('menu.listYourBusiness') </a>
                        </li>

                    </ul>
                    <ul class="nav navbar-right">
                      <li class="nav-item d-flex justify-content-center cart_nav position-relative">
                      <a class="nav-link align-items-center d-flex" href="{{ route('front.cartPage') }}"><i class="zmdi zmdi-shopping-cart"></i>@lang('front.cart')</a><span class="cart-badge cart-badge">{{ $productsCount }}</span>
                      </li>
                  </ul>
                </div>
            </nav>
            <!-- DESKTOP NAVBAR END -->

            <!-- MOBILE NAVBAR START -->
            <div class="d-block d-md-block d-lg-none mobile_navbar position-relative" id="header">
                <div id="mySidenav" class="sidenav ">
                    <a href="javascript:void(0)" class="closebtn close-nav"><i class="zmdi zmdi-close-circle"></i></a>
                    <nav>
                        <ul class="mobile_menu">
                            <li>
                              <a href="{{ route('front.index') }}" class="navLogo">
                                <img src="{{asset('front/images/pixel.gif')}}" data-src="{{ $frontThemeSettings->logo_url }}" alt="Logo" width="auto" />
                              </a>
                            </li>

                            <li>
                                <label for="drop-00" class="toggle"> @lang('app.categories') +</label>
                                <input type="checkbox" id="drop-00">
                                <ul>
                                  @foreach ($headerCategories as $category)
                                    <li>
                                        <a href="/{{$category->slug}}/services">{{ $category->name }}</a>
                                    </li>
                                  @endforeach
                                </ul>
                            </li>

                            <li><a href="{{ route('front.deals') }}">@lang('menu.deals')</a></li>
                            <li><a href="{{ route('front.blog') }}">{{__('menu.blog')}}</a></li>
                            <li><a href="{{ route('front.page', 'contact-us') }}">@lang('app.contactUs')</a></li>
                            <li><a href="{{ route('front.register') }}">@lang('menu.listYourBusiness')</a></li>
                          </ul>
                    </nav>
                </div>

            </div>
            <!-- MOBILE NAVBAR END -->

        </div>
    </div>

    @endif

</header>
<!-- HEADER END -->

@push('footer-script')
    <script>

        $('body').on('click', '.front-logout', function(e) {
            e.preventDefault();
            $('#logoutForm').submit();
        });

        var substringMatcher = function(strs) {
            return function findMatches(q, cb) {
                var matches, substringRegex;

                // an array that will be populated with substring matches
                matches = [];

                // regex used to determine if a string contains the substring `q`
                substrRegex = new RegExp(q, 'i');

                // iterate through the pool of strings and for any string that
                // contains the substring `q`, add it to the `matches` array
                $.each(strs, function(i, str) {
                    if (substrRegex.test(str)) {
                        matches.push(str);
                    }
                });

                cb(matches);
            };
        };

        $(function () {
            $('.myselect').select2();

            $('.categoty-select').select2({
                placeholder: "Categories",
            });

            if (localStorage.getItem('location') == null)
            {
                let locations_html = '';
                $.easyAjax({
                    url: '{{ route('front.get-all-locations') }}',
                    type: 'GET',
                    blockUI: false,
                    success: function (response)
                    {
                        console.log(response.locations);
                        if (response.locations.length > 0) {
                            response.locations.forEach(location => {
                                    locations_html += `<a class="search-tags" data-location-id="${location.id}">${location.name}</a>`
                            });
                        }
                        $('.locationPlaces').html(locations_html);
                        $('#myModal').modal('show');
                    }
                })
            }else{
                var locationId = localStorage.getItem('location')
                $('#location_mobile option[value='+locationId+']').attr('selected','selected');
            }

            $('body').on('click', '.search-tags', function () {
                let locationId = $(this).data('location-id');

                $('#location').val(locationId);
                localStorage.setItem('location', locationId);
                location.reload();
            });

            if (localStorage.getItem('location')) {
                $('#location').val(localStorage.getItem('location')).trigger('change');
            }

            $('#location, #location_mobile').on('change', function()
            {
                localStorage.setItem('location', $(this).val());
                $('#location').val($(this).val());
                if (localStorage.getItem('location') !== '' && location.protocol+'//'+location.hostname+location.pathname == '{{ route('front.search') }}') {
                    $('#searchForm').submit();
                }
                location.reload();
            });

            let searchParams = new URLSearchParams(window.location.search);
            if (searchParams.has('q')) {
                $('#search_term').val(searchParams.get('q'));
            }
        });

        $('#language, #language_mobile').on('change', function() {
            let code = $(this).val();

            let url = '{{ route('front.changeLanguage', ':code') }}';
            url = url.replace(':code', code);

            if (!$(this).hasClass('active')) {
                $.easyAjax({
                    url: url,
                    type: 'POST',
                    container: 'body',
                    blockUI: false,
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        location.reload();
                    }
                })
            }
        });

        @if(!request()->is('register'))
        $(document).ready(function()
        {
            $("#globalSearch").autocomplete({
                source: function(request, response) {

                    var term = $('#globalSearch').val();

                    $.getJSON("{{ route('front.globalSearch') }}", { term : term, location: localStorage.getItem('location') }, response);
                },
                focus: function( event, ui ) {
                    $("#globalSearch").val( ui.item.title );
                    return false;
                },
                select: function( event, ui ) {
                    $("#searchForm").attr("action", ui.item.url);
                    $("#searchForm").submit();
                },
                classes: {
                    "ui-autocomplete": "highlight"
                },
            }).data("ui-autocomplete")._renderItem = function(ul, item) {
                var inner_html = '<a href="' + item.url + '" ><div class="d-flex align-item-center list_item_container p-10"><div class="image"><img class="img img-thumbnail dim" src="' + item.image + '" ></div><div class="label"><b>' + item.title + '</b><p>'+ item.category +'</p></div></div></a>';
                return $("<li></li>")
                    .data( "item.autocomplete", item)
                    .append(inner_html)
                    .appendTo(ul);
            };

            $("#globalSearchMobile").autocomplete({
                source: function(request, response) {

                let term = $('#globalSearchMobile').val();

                $.getJSON("{{ route('front.globalSearch') }}", { term : term, location: localStorage.getItem('location') },
                        response);
                },
                focus: function( event, ui ) {
                    $("#globalSearchMobile").val( ui.item.title );
                    return false;
                },
                select: function( event, ui ) {
                    $("#searchFormMobile").attr("action", ui.item.url);
                    $("#searchFormMobile").submit();
                },
                classes: {
                    "ui-autocomplete": "highlight"
                },
            }).data("ui-autocomplete")._renderItem = function(ul, item) {
                var inner_html = '<a href="' + item.url + '" ><div class="d-flex align-item-center list_item_container p-10"><div class="image"><img class="img img-thumbnail dim" src="' + item.image + '" ></div><div class="label"><b>' + item.title + '</b><p>'+ item.category +'</p></div></div></a>';

                return $("<li></li>")
                    .data( "item.autocomplete", item )
                    .append(inner_html)
                    .appendTo( ul );
            };

        });

        @endif
        /* this function will call on page reload */
        function ajax()
        {
            var location_id = localStorage.getItem('location');
            if ( location_id !== '' && location.href == '{{ route('front.index').'/' }}')
            {
                var url = '{{ route('front.index', ['location' => 'variable']) }}';
                url = url.replace('variable', location_id);

                $.easyAjax({
                    url: url,
                    type: 'GET',
                    data: {location_id : location_id},
                    blockUI: false,
                    success: function (response)
                    {
                        console.log(response.articles);
                        // CATEGORIES START
                            @if (array_search('Category Section', array_column($sections, 'name')) !== false)
                                if(response.categories.length>0)
                                {
                                    let categories = `
                                    <div class="container">
                                        <div class="heading justify-content-lg-center">
                                            <p class="mb-0">@lang('front.chooseYourCategory')</p>
                                        </div>
                                        <div class="row">`;

                                    response.categories.forEach(category => {
                                    if (category.services.length > 0)
                                    {
                                        categories += `
                                        <div class="col-md-3 col-6 mb-4">
                                        <div class="categoryBox">
                                            <a href="/`+category.slug+`/services">
                                                <img src="{{asset('front/images/pixel.gif')}}" data-src="`+category.category_image_url+`" class="img-fluid" alt="Cafes" />
                                                    <div class="category_box_hover">
                                                        <p class="number_of_category">`+category.services_count+`</p>
                                                        <p class="category_name">`+category.name+`</p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>`
                                    }
                                    });

                                    let viewAllCategories = response.total_categories_count > 8 ? `<div class="row justify-content-center mt-3">
                                    <a href="{{ route('front.services', 'all') }}" class="view_all hvr-radial-out">{{__('app.viewAll')}}</a>
                                    </div>` : '';

                                    categories = categories+`</div>`+viewAllCategories+`</div>`;

                                    $('#categorySection').show();
                                    $('#categorySection').html(categories);

                                } else {
                                    $('#categorySection').hide();
                                }
                            @endif
                        // CATEGORIES END

                        // SPOTLIGHT START
                            @if (array_search('Spotlight Section', array_column($sections, 'name')) !== false)
                            if(response.spotlight.length>0)
                            {
                                var slider_length = $('.spot_box').length;
                                for (var i=0; i<slider_length; i++) {
                                    $("#spotlight_slider").trigger('remove.owl.carousel',[i]).trigger('refresh.owl.carousel');
                                }

                                response.spotlight.forEach(spotlight => {
                                let detail_url = spotlight.deal !='' ? spotlight.deal.deal_detail_url : '';
                                let image_url = spotlight.deal!='' ? spotlight.deal.deal_image_url : ''

                                $('#spotlight_slider').trigger('add.owl.carousel', [jQuery(` <div class="item spot_box">
                                    <div class="spot_box_img">
                                        <a class="ml-auto" href="${detail_url}">
                                            <img src="{{asset('front/images/pixel.gif')}}" data-src="${image_url}" alt="Image" />
                                        </a>
                                    </div>
                                <a class="ml-auto" href="${detail_url}">
                                    <h2 class="px-4 pt-4">${spotlight.deal.title}</h2>
                                </a>
                                <p class="px-4">@lang('app.starting') @lang('app.from'):&nbsp;<span class="spotlightPrice">${spotlight.deal.formated_deal_amount} </span>
                                    <br> <a href="{{route('front.vendorPage','').'/'}}${spotlight.company.slug}"><span class="spotlightName">${spotlight.company.company_name}</span></a></p>
                                    <div class="d-flex px-2">
                                        <div class="col-md-12 spot_deal pt-2">
                                            <a
                                                href="javascript:;"
                                                class="ml-auto add-to-cart w-100"
                                                data-type="deal"
                                                data-unique-id="deal${spotlight.deal.id}"
                                                data-id="${spotlight.deal.id}"
                                                data-price="${parseFloat(spotlight.deal.converted_deal_amount).toFixed(2)}"
                                                data-name="${spotlight.deal.title}"
                                                data-company-id="${spotlight.company.id}"
                                                id="spotlight${spotlight.deal.id}"
                                                data-max-order="${spotlight.deal.max_order_per_customer}"
                                                aria-expanded="false">
                                                @lang('front.addToCart')
                                            </a>
                                        </div>
                                    </div>
                                </div>`)]).trigger('refresh.owl.carousel');
                                });

                                $('#spotlightSection').show();

                            } else {
                                $('#spotlightSection').hide();
                            }
                            @endif

                        // SPOTLIGHT END
                        
                        // BLOG SECTION
                            @if (array_search('Blog Section', array_column($sections, 'name')) !== false)
                                if (response.articles.length > 0) {
                                    var slider_length = $('.spot_box').length;
                                    for (var i = 0; i < slider_length; i++) {
                                        $("#articles_slider").trigger('remove.owl.carousel', [i]).trigger('refresh.owl.carousel');
                                    }

                                    response.articles.forEach(article => {
                                        let detail_url = '/blog/'+article.slug;
                                        let image_url = article.article_image_url;

                                        $('#articles_slider').trigger('add.owl.carousel', [jQuery(` <div class="item spot_box">
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
                            @endif
                        // BLOG END

                        /* DEAL START */
                            @if (array_search('Deal Section', array_column($sections, 'name')) !== false)
                                if(response.deals.length>0)
                                {
                                    var slider_length = $('.deal_item').length;
                                    for (var i=0; i<slider_length; i++) {
                                        $("#featured_deal_slider").trigger('remove.owl.carousel', [i]).trigger('refresh.owl.carousel');
                                    }

                                    response.deals.forEach(deal => {
                                    let deal_type = deal.deal_type=='' ? "{{__('app.offer')}}" : "{{__('app.combo')}}";

                                    $('#featured_deal_slider').trigger('add.owl.carousel', [jQuery(`<div class="item d-flex deal_item">
                                        <div class="media">
                                            <div class="featured_deal_imgBox">
                                                <a href="${deal.deal_detail_url}">
                                                    <img src="{{asset('front/images/pixel.gif')}}" data-src="${deal.deal_image_url}" alt="Deal1">
                                                </a>
                                            </div>
                                            <div class="media-body featuredDealDetail position-relative ">
                                                <span class="tag">${deal_type}</span>

                                               <a  class="featuredHeading" href="{{route('front.vendorPage','').'/'}}${deal.company.slug}">${deal.company.company_name}</a>

                                                <h1>${deal.title}</h1>
                                                <p class="mb-lg-1 mb-xl-3">${deal.formated_deal_amount} &nbsp;&nbsp;<span>${deal.formated_original_amount}</span></p>
                                                <a
                                                    href="javascript:;"
                                                    class="btn w-100 add-to-cart"
                                                    data-type="deal"
                                                    data-unique-id="deal${deal.id}"
                                                    data-id="${deal.id}"
                                                    data-price="${parseFloat(deal.converted_deal_amount).toFixed(2)}"
                                                    data-name="${deal.title}"
                                                    data-company-id="${deal.company.id}"
                                                    id="deal${deal.id}"
                                                    data-max-order="${deal.max_order_per_customer}"
                                                    aria-expanded="false">
                                                    @lang('front.addToCart')
                                                </a>
                                            </div>
                                        </div>
                                    </div>`)]).trigger('refresh.owl.carousel');
                                    });

                                    $('#featuredDeals').show();

                                    response.total_deals_count> 8 ? $('#view_all_deals_btn').show() : $('#view_all_deals_btn').hide();
                                } else {
                                    $('#featuredDeals').hide();
                                }
                            @endif
                        /* DEAL START */
                        lazyload();
                    } /* success closing */
                })
            }
        }

         // add items to cart
        $('body').on('click', '.add-to-cart', function ()
        {

            let element_id = $(this).attr('id');
            let type = $(this).data('type');
            let unique_id = $(this).data('unique-id');
            let companyId = $(this).data('company-id');
            let id = $(this).data('id');
            let name = $(this).data('name');
            let price = $(this).data('price');
            let token = $("meta[name='csrf-token']").attr('content');

            if(type == 'deal')
            {
                var max_order = $(this).data('max-order');
            }

            var data = {id, type, price, name, companyId, unique_id, max_order, _token: $("meta[name='csrf-token']").attr('content')};

            $.easyAjax({
                url: '{{ route('front.addOrUpdateProduct') }}',
                type: 'POST',
                data: data,
                blockUI: false,
                disableButton: true,
                buttonSelector: "#"+element_id,
                defaultTimeout: '1000',
                success: function (response) {
                    if(response.result=='fail' || response.result=='typeerror')
                    {
                        swal({
                            title: "@lang('front.buttons.clearCart')?",
                            text: response.message,
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
                                    blockUI: false,
                                    disableButton: true,
                                    buttonSelector: "#"+element_id,
                                    success: function (response) {
                                        if (response.status == 'success') {
                                            $.easyAjax({
                                                url: '{{ route('front.addOrUpdateProduct') }}',
                                                type: 'POST',
                                                data: data,
                                                blockUI: false,
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
                }
            })
        });

    </script>
@endpush
