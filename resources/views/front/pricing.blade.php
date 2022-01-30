@extends('layouts.front')

@push('styles')
    <link href="{{ asset('front/css/all_deals.css') }}" rel="stylesheet">
    <link href="{{ asset('front/css/pricing.css') }}" rel="stylesheet">

    @if(count($packages) > 0)
        <style>
            .package-column {
                max-width: {{ 100/count($packages) }}%;
                flex: 0 0 {{ 100/count($packages) }}%;
            }
            .row::-webkit-scrollbar {
                height: 10px !important;
            }
        </style>
    @endif

    <style>
        .card-header {
            background-color: rgba(249, 243, 243, 0.03);
        }
        .faq .card .card-header::after {
            color: #313130;
            content: '\f2f9' !important;
            display: inline-block;
            font: normal normal normal 18px/52px 'Material-Design-Iconic-Font';
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .card.in .card-header[data-toggle=collapse]::after {
            content: '\f106' !important;
        }
        .card .card-header[data-toggle=collapse]::after {
            position: absolute;
            right: 0;
            top: 0;
            padding-right: 1.725rem;
            line-height: 51px;
            font-weight: 900;
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            color: #D1D3E2;
        }
        .tabpanel-container{
            visibility: visible;
            animation-name: fadeIn;
        }
        .mark-recommended-border {
            border: 1px solid #54a6f1 !important;
        }
        .mark-recommended-background {
            background-color: rgb(84, 166, 241);
        }
    </style>
@endpush

@section('content')
    <!-- BREADCRUMB START -->
        <section class="breadcrumb_section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 col-md-5">
                        <h1 class="mb-0">@lang('app.price')</h1>
                    </div>
                    <div class="col-lg-5 col-md-7">
                        <nav>
                            <ol class="breadcrumb mb-0 justify-content-center">
                                <li class="breadcrumb-item"><a href="/">@lang('app.home')</a></li>
                                <li class="breadcrumb-item active"><span>@lang('app.price')</span></li>
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
                    <div class="col-md-12">
                        <h3 class="text-center">@lang('app.chooseRightPlan')</h3>
                        <p class="text-center">@lang('app.findPerfectMatch')</p>
                    </div>

                    <div class="col-md-12 text-center mb-5 mt-5">
                        <div class="nav price-tabs justify-content-center" role="tablist">
                            <a class="nav-link active" href="#monthly" role="tab" data-toggle="tab">@lang('app.monthly')</a>
                            <a class="nav-link " href="#yearly" role="tab" data-toggle="tab">@lang('app.annual')</a>
                        </div>
                    </div>

                    <div class="col-md-12 ">
                        <div class="tab-content wow fadeIn tabpanel-container">

                            <div role="tabpanel" class="tab-pane fade active show" id="monthly">
                                <div class="container">
                                    <div class="price-wrap border row no-gutters">
                                        <div class="diff-table col-6 col-md-3">
                                            <div class="price-top">
                                                <div class="price-top title">
                                                    <h3>@lang('app.pickUp') <br> @lang('app.yourPlan')</h3>
                                                </div>
                                                <div class="price-content">
                                                    <ul>
                                                        <li>
                                                            @lang('app.maxEmployees')
                                                        </li>
                                                        <li>
                                                            @lang('app.maxDeals')
                                                        </li>
                                                        <li>
                                                            @lang('app.maxServices')
                                                        </li>
                                                        <li>
                                                            @lang('app.roleNpermissions')
                                                        </li>
                                                        <li>
                                                            @lang('app.report')
                                                        </li>
                                                        <li>
                                                            @lang('app.pos')
                                                        </li>
                                                        <li>
                                                            @lang('app.employeeLeave')
                                                        </li>
                                                        <li>
                                                            @lang('app.employeeSchedule')
                                                        </li>
                                                        <li>
                                                            @lang('app.googleCalendar')
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="all-plans col-6 col-md-9">
                                            <div class="row no-gutters flex-nowrap flex-wrap overflow-x-auto row-scroll">

                                                @foreach ($packages as $package)
                                                    @if($package->make_private == 'false')
                                                    <div class="col-md-3 package-column">
                                                        <div class="pricing-table price- @if($package->mark_recommended == 'true') mark-recommended-border @endif">
                                                            <div class="price-top">
                                                                <div class="price-head text-center @if ($package->mark_recommended == 'true') mark-recommended-background @endif">
                                                                    <h5 class="mb-0">{{ $package->name }}</h5>
                                                                </div>
                                                                <div class="rate">
                                                                    <h2 class="mb-2">
                                                                        <sup>{{ $settings->currency->currency_symbol }}</sup>
                                                                        <span
                                                                            class="font-weight-bolder">{{ $package->monthly_price }}</span>
                                                                    </h2>
                                                                    <p class="mb-0">@lang('app.billed') @lang('app.monthly')</p>
                                                                </div>
                                                            </div>
                                                            <div class="price-content">
                                                                <ul>
                                                                    <li>
                                                                        {{ $package->max_employees }} @lang('app.members')
                                                                    </li>
                                                                    <li>
                                                                        {{ $package->max_deals }}
                                                                    </li>
                                                                    <li>
                                                                        {{ $package->max_services }}
                                                                    </li>
                                                                    <li>
                                                                        {{ $package->max_roles }}
                                                                    </li>
                                                                    <li>
                                                                        @php
                                                                            $package_modules = !is_null($package->package_modules) ? json_decode($package->package_modules, true) : [];
                                                                        @endphp
                                                                        @if (in_array('Reports', $package_modules))
                                                                            <i class="zmdi zmdi-check-circle blue"></i>
                                                                        @else
                                                                            <i class="zmdi zmdi-close-circle"></i>
                                                                        @endif
                                                                    </li>
                                                                    <li>
                                                                        @if (in_array('POS', $package_modules))
                                                                            <i class="zmdi zmdi-check-circle blue"></i>
                                                                        @else
                                                                            <i class="zmdi zmdi-close-circle"></i>
                                                                        @endif
                                                                    </li>
                                                                    <li>
                                                                        @if (in_array('Employee Leave', $package_modules))
                                                                            <i class="zmdi zmdi-check-circle blue"></i>
                                                                        @else
                                                                            <i class="zmdi zmdi-close-circle"></i>
                                                                        @endif
                                                                    </li>
                                                                    <li>
                                                                        @if (in_array('Employee Schedule', $package_modules))
                                                                            <i class="zmdi zmdi-check-circle blue"></i>
                                                                        @else
                                                                            <i class="zmdi zmdi-close-circle"></i>
                                                                        @endif
                                                                    </li>
                                                                    <li>
                                                                        @if (in_array('Google Calendar', $package_modules))
                                                                            <i class="zmdi zmdi-check-circle blue"></i>
                                                                        @else
                                                                            <i class="zmdi zmdi-close-circle"></i>
                                                                        @endif
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane fade" id="yearly">
                                <div class="container">
                                    <div class="price-wrap border row no-gutters">
                                        <div class="diff-table col-6 col-md-3">
                                            <div class="price-top">
                                                <div class="price-top title">
                                                    <h3>@lang('app.pickUp') <br> @lang('app.yourPlan')</h3>
                                                </div>
                                                <div class="price-content">
                                                    <ul>
                                                        <li>
                                                            @lang('app.maxEmployees')
                                                        </li>
                                                        <li>
                                                            @lang('app.maxDeals')
                                                        </li>
                                                        <li>
                                                            @lang('app.maxServices')
                                                        </li>
                                                        <li>
                                                            @lang('app.roleNpermissions')
                                                        </li>
                                                        <li>
                                                            @lang('app.report')
                                                        </li>
                                                        <li>
                                                            @lang('app.pos')
                                                        </li>
                                                        <li>
                                                            @lang('app.employeeLeave')
                                                        </li>
                                                        <li>
                                                            @lang('app.employeeSchedule')
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="all-plans col-6 col-md-9">
                                            <div class="row no-gutters flex-nowrap flex-wrap overflow-x-auto row-scroll">
                                                @foreach ($packages as $package)
                                                    @if($package->make_private == 'false')
                                                    <div class="col-md-3 package-column">
                                                        <div class="pricing-table price- @if($package->mark_recommended == 'true') mark-recommended-border @endif">
                                                            <div class="price-top">
                                                                <div class="price-head text-center">
                                                                    <h5 class="mb-0">{{ $package->name }}</h5>
                                                                </div>
                                                                <div class="rate">
                                                                    <h2 class="mb-2">
                                                                        <sup>{{ $settings->currency->currency_symbol }}</sup>
                                                                        <span
                                                                            class="font-weight-bolder">{{ $package->annual_price }}</span>
                                                                    </h2>
                                                                    <p class="mb-0">@lang('app.billed') @lang('app.yearly')</p>
                                                                </div>
                                                            </div>
                                                            <div class="price-content">
                                                                <ul>
                                                                    <li>
                                                                        {{ $package->max_employees }} @lang('app.members')
                                                                    </li>
                                                                    <li>
                                                                        {{ $package->max_deals }}
                                                                    </li>
                                                                    <li>
                                                                        {{ $package->max_services }}
                                                                    </li>
                                                                    <li>
                                                                        {{ $package->max_roles }}
                                                                    </li>
                                                                    <li>
                                                                        @if (in_array('Reports', $package_modules))
                                                                            <i class="zmdi zmdi-check-circle blue"></i>
                                                                        @else
                                                                            <i class="zmdi zmdi-close-circle"></i>
                                                                        @endif
                                                                    </li>
                                                                    <li>
                                                                        @if (in_array('POS', $package_modules))
                                                                            <i class="zmdi zmdi-check-circle blue"></i>
                                                                        @else
                                                                            <i class="zmdi zmdi-close-circle"></i>
                                                                        @endif
                                                                    </li>
                                                                    <li>
                                                                        @if (in_array('Employee Leave', $package_modules))
                                                                            <i class="zmdi zmdi-check-circle blue"></i>
                                                                        @else
                                                                            <i class="zmdi zmdi-close-circle"></i>
                                                                        @endif
                                                                    </li>
                                                                    <li>
                                                                        @if (in_array('Employee Schedule', $package_modules))
                                                                            <i class="zmdi zmdi-check-circle blue"></i>
                                                                        @else
                                                                            <i class="zmdi zmdi-close-circle"></i>
                                                                        @endif
                                                                    </li>
                                                                    <li>
                                                                        @if (in_array('Google Calendar', $package_modules))
                                                                            <i class="zmdi zmdi-check-circle blue"></i>
                                                                        @else
                                                                            <i class="zmdi zmdi-close-circle"></i>
                                                                        @endif
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    @if ($frontFaqs->count() > 0)
                        <div class="col-md-12 mt-5 mb-5">
                            <br><br><br>
                            <h3 class="pt-7 mb-5 text-center">@lang('app.freaquentlyAskedQuestions')</h3>

                            <div class="container-fluid">
                                <div class="col-md-12">
                                    <div id="accordion">

                                        @foreach ($frontFaqs as $index => $faq)
                                            <div class="card mb-3 @if ($index==0) in @endif" id="card1">
                                                <div class="card-header d-flex align-items-center" data-toggle="collapse"
                                                    data-target="#collapse{{ $index }}" aria-expanded="true"
                                                    aria-controls="collapseOne" id="heading1">
                                                    <h5 class="mb-0"># {!! $faq->question !!}</h5>
                                                </div>
                                                <div id="collapse{{ $index }}" class="collapse @if ($index==0) show @endif" aria-labelledby="heading1"
                                                    data-parent="#accordion">
                                                    <div class="card-body">
                                                        {!! $faq->answer !!}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    <!-- ALL DEALS END -->
@endsection

@push('footer-script')
    <script>
        $('body').on('click', '.card-header', function() {
            var a = $(this).attr('id').split('heading', 2);
            if ($("#card" + a[1]).hasClass("in") == true) {
                $("#card" + a[1]).removeClass("in");
            } else {
                $(".card").removeClass("in");
                $("#card" + a[1]).addClass("in");
            }
        });
    </script>
@endpush
