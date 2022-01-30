<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- SEO -->
        <meta name='description' content='{{$vendorPage->seo_description ??  $frontThemeSettings->seo_description}}' />
        <meta name='keywords' content='{{$vendorPage->seo_keywords ?? $frontThemeSettings->seo_keywords}}' />
        <title>{{$company->company_name ?? $frontThemeSettings->title}}</title>

        <meta property="og:title" content="{{$company->company_name ?? $frontThemeSettings->title}}" />
        <meta property="og:description" content="{{$vendorPage->seo_description ??  $frontThemeSettings->seo_description}}" />
        <meta property="og:url" content="{{url()->current()}}" />
        @if ($vendorPage->og_image ?? false)
        <meta property="og:image" content="{{$vendorPage->og_image}}" />
        @endif

        <!-- Bootstrap 4 core CSS -->
        <link href="{{ asset('front/css/bootstrap.min.css') }}" rel="stylesheet">

        <!-- Favicon icon -->
        <link rel="icon" href="{{$frontThemeSettings->favicon_url}}" type="image/x-icon" />

        <!-- Custom style -->
        <link href="{{ asset('front/css/style.css') }}" rel="stylesheet">

        <!-- Old Css Files -->
        <link type="text/css" rel="stylesheet" href="{{ asset('front-assets/css/helper.css') }}">

        <!-- Responsive style -->
        <link href="{{ asset('front/css/responsive.css') }}" rel="stylesheet">

        <link href="{{ asset('front/css/all.css') }}" rel="stylesheet">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Lato&family=Poppins:wght@400;500&family=Quicksand:wght@700&display=swap" rel="stylesheet">

        <!-- Material Icons -->
        <link href="{{ asset('front/css/material-design-iconic-font.min.css') }} " rel="stylesheet">

        <!-- Owl Stylesheets -->
        <link rel="stylesheet" href="{{ asset('front/css/owl.carousel.min.css') }} ">
        <link rel="stylesheet" href="{{ asset('front/css/owl.theme.default.min.css') }} ">

        <!-- Select To Plugin -->
        <link rel="stylesheet" href="{{ asset('front/css/select2.min.css') }} ">

        {{-- Live search with image --}}
        <link rel="stylesheet" href="{{ asset('front/css/jquery-ui.css') }} ">

        <style>
            :root {
                --primary-color: {{ $frontThemeSettings->primary_color }};
                --dark-primary-color: {{ $frontThemeSettings->primary_color }};
                --secondary-color: {{ $frontThemeSettings->secondary_color }};
                --dark-secondary-color: {{ $frontThemeSettings->secondary_color }};
            }

            {!! $frontThemeSettings->custom_css !!}

            .loader {
                border: 4px solid #F3F3F3;
                border-radius: 60%;
                border-top: 4px solid var(--primary-color);
                width: 30px;
                height: 30px;
                -webkit-animation: spin 2s linear infinite; /* Safari */
                animation: spin 3s linear infinite;
                }
                /* Safari */
                @-webkit-keyframes spin {
                0% { -webkit-transform: rotate(0deg); }
                100% { -webkit-transform: rotate(360deg); }
                }
                @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Gloabal Search css */
            .ui-menu-item:hover, .ui-menu-item:focus, .ui-menu-item-wrapper:active {
                /* background-color: #00c1cf; */
                background-color: var(--primary-color);
            }
            .label {
                margin-left: 10px;
            }
            .ui-menu .ui-menu-item-wrapper {
                padding : 0px;
            }
            .ui-widget {
                font-family: Quicksand,sans-serif;
                font-size: 15px;
            }
            .list_item_container p {
                font-size: 16px;
                line-height: 1.38;
                color: #979797
            }
            .ui-widget-content {
                background-color: #fff ;
            }

            .ui-state-active,
            .ui-widget-content .ui-state-active,
            .ui-widget-header .ui-state-active,
            a.ui-button:active,
            .ui-button:active,
            .ui-button.ui-state-active:hover, .ui-menu-item-wrapper.ui-state-active{
                background: var(--primary-color) !important;
                padding: 1px !important;
            }
            .ui-menu-item-wrapper{
                display: block;
                border: 0px !important;
            }
            #myModal {
                padding-right: 0px !important;
            }
            #modal-dialog-div {
                max-width: 100%;
                margin: 0;
            }
            .noResultFound {
                margin-top:30px;
            }
            .btn-dark {
                color: #fff;
                background-color: var(--secondary-color);
                border-color: var(--secondary-color);
            }
        </style>
        @stack('styles')

        @if (file_exists(public_path('custom/style.css')))
            <link rel="stylesheet" href="{{ asset('custom/style.css') }} ">
        @endif

        @if (file_exists(public_path('custom/header.js')))
            <script src="{{asset('custom/header.js')}}"></script>
        @endif
    </head>

    <body>

        @include('sections.header')

        @yield('content')

        @include('sections.footer')

        @include('front.coupon-model')

        <!-- Location Modal Start -->
        <div class="header_location_modal modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog" id="modal-dialog-div">
            <div class="modal-content">
                <div class="modal-body text-center p-5">
                    <h4>@lang('front.pickCity')</h4>
                    <small class="text-muted">@lang('front.pickCityNote')</small>
                    <div class="locationPlaces mt-5"></div>
                </div>
            </div>
            </div>
        </div>
        <!-- Location Modal End -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->

    <script src="{{asset('assets/js/front-scripts.js')}}"></script>
    <script src="{{ asset('front-assets/js/helper.js') }}"></script>
    <script src="{{asset('front/js/holder.min.js')}}"></script>
    <script src="{{asset('front/js/owl.carousel.js')}}"></script>
    <script src="{{asset('front/js/select2.min.js')}}"></script>
    <script src="{{asset('front/js/main.js')}}"></script>
    <script src="{{asset('front/js/c7cfa3a8ca.js')}}"></script>
    <script src="{{asset('front/js/sweetalert.min.js')}}"></script>

    {{-- live search with image --}}
    <script src="{{asset('front/js/jquery-ui.js')}}"></script>

    {{-- Lazy load --}}
    <script src="{{ asset('front/js/jquery.lazy.min.js') }}"></script>

    @if (file_exists(public_path('custom/footer.js')))
    <script src="{{asset('custom/footer.js')}}"></script>
    @endif

    @forelse ($widgets as $widget)
    {!! $widget->code !!}
    @empty
    @endforelse
     <script>
        "use strict";
        // Lazy load function call it when you load images on ajax it will get data-src data and put in src
        function lazyload(){
            $('img').Lazy({
                // your configuration goes here
                scrollDirection: 'vertical',
                effect: 'fadeIn',
                visibleOnly: true,
                onError: function(element) {
                    console.log('error loading ' + element.data('src'));
                }
            });
        }
        lazyload();

        $(function() {
            toastr.options = {
                "progressBar": true,
                "positionClass": "toast-bottom-right",
                "preventDuplicates": true
            };
        });

        function makeSingular(time, type) {
            singular = '';
            plural = '';

            if (time == 1) {
                switch (type) {
                    case 'minutes':
                        singular = "@lang('app.minute')";
                        break;
                    case 'hours':
                        singular = "@lang('app.hour')";
                        break;
                    case 'days':
                        singular = "@lang('app.day')";
                        break;
                    default:
                        break;
                }
                return singular;
            }
            else {
                switch (type) {
                    case 'minutes':
                        plural = "@lang('app.minutes')";
                        break;
                    case 'hours':
                        plural = "@lang('app.hours')";
                        break;
                    case 'days':
                        plural = "@lang('app.days')";
                        break;
                    default:
                        break;
                }
                return plural;
            }
        }

        function goToPage(method, pageUrl, data = null) {
            var options = {
                url: pageUrl,
                type: method,
                success: function (response) {
                    if (response.status !== 'fail') {
                        window.location.href = pageUrl
                    }
                }
            };

            if (data) {
                options.data = data
            }

            $.easyAjax(options)
        }

        var LightenColor = function(color, percent) {
            var num = parseInt(color.replace('#',''),16),
                amt = Math.round(2.55 * percent),
                R = (num >> 16) + amt,
                B = (num >> 8 & 0x00FF) + amt,
                G = (num & 0x0000FF) + amt;

            return (0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (B<255?B<1?0:B:255)*0x100 + (G<255?G<1?0:G:255)).toString(16).slice(1);
        };

        var DarkenColor = function(color, percent) {
            var num = parseInt(color.replace('#',''),16),
                amt = Math.round(2.55 * percent),
                R = (num >> 16) - amt,
                B = (num >> 8 & 0x00FF) - amt,
                G = (num & 0x0000FF) - amt;

            return (0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (B<255?B<1?0:B:255)*0x100 + (G<255?G<1?0:G:255)).toString(16).slice(1);
        };

        var primaryColor = getComputedStyle(document.documentElement)
            .getPropertyValue('--primary-color');

        document.documentElement.style.setProperty('--dark-primary-color', '#'+DarkenColor(primaryColor, 15));


        $('body').on('click', '.show_latest_coupon_code', function() {
            const coupon_id = $(this).data('coupon-id');
            const coupon_title = $(this).data('coupon-title');
            const coupon_description = $(this).data('coupon-description');
            const coupon_code = $(this).data('coupon-code');

            $('#coupon_title').html(coupon_title);
            $('#coupon_detail').html(coupon_description);
            $('#coupon_code').html(coupon_code);
            $('#coupon_code').val(coupon_code);
            $('#coupon_code_copy_btn').html('copy');

            if($('#detail_button').html()!='Show Detail') {
                $('#detail_button').click();
            }

            var buttonId = $(this).attr('id');
            $('#latest_coupon_modal_container').removeAttr('class').addClass(buttonId);
            $('body').addClass('modal-active');

        });

        $('body').on('click', '.close_coupon_modal', function() {
            $('#latest_coupon_modal_container').addClass('out');
            $('body').removeClass('modal-active');
        });

        $('body').on('click', '#detail_button', function() {
            if($('#detail_button').html()=='Show Detail') {
                $('#detail_button').html('Hide Detail');
            }
            else {
                $('#detail_button').html('Show Detail');
            }
            $('#coupon_detail').toggle();
        });

        $('body').on('click', '#coupon_code_copy_btn', function() {
            var copyText = document.getElementById("coupon_code");

            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); /*For mobile devices*/

            /* Copy the text inside the text field */
            document.execCommand("copy");

            $('#coupon_code_copy_btn').html('copied');
        });

    </script>

    @stack('footer-script')

  </body>
</html>

