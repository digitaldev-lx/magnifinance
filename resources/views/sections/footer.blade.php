<!-- FOOTER START -->
<footer>
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <h2>@lang('front.popularSearch')</h2>
                    <ul class="px-0 mb-0 mt-4">
                        @foreach ($popularSearch as $popularSearches)
                        <li><a href="{{ route('front.search', ['l' => $popularSearches->location_id, 'q' => $popularSearches->title, 'term' => $popularSearches->title]) }}"><span></span>{{ ucwords($popularSearches->title) }} </a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-3 col-6">
                    <h2>@lang('front.usefulLinks')</h2>
                    <ul class="px-0 mb-0 mt-4">
                        <li><a href="{{ route('front.cartPage') }}"><span></span>@lang('front.navigation.toCart')</a></li>
                        <li><a href="{{ route('front.pricing') }}"><span></span>@lang('app.pricing')</a></li>
                        @foreach ($pages as $page)
                            <li><a href="/{{ $page->slug }}"><span></span>{{ ucwords($page->title) }} </a></li>
                        @endforeach
                        <li><a href="{{ route('login') }}"><span></span>@lang('app.login') / @lang('front.register')</a></li>
                        <li><a href="{{ route('front.register') }}"><span></span>@lang('front.becomeVendor')</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-6">
                    <h2>@lang('front.popularStores')</h2>
                    <ul class="px-0 mb-0 mt-4">
                        @foreach ($popularStores as $store)
                        <li><a href="{{ route('front.vendorPage', $store->slug) }}"><span></span>{{ ucwords($store->company_name) }} </a></li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-3 col-6 socialLinks">
                    <h2 class="mb-4">@lang('front.getSocial')</h2>
                    @foreach ($footer_setting->social_links as $link)
                    @if($link['link'] !== null)
                        <a href="{{ $link['link'] }}" class="justify-content-center align-items-center" target="_blank">
                            <i class="zmdi zmdi-hc-lg zmdi-{{ $link['name'] }}"></i>
                        </a>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="footer_bottom ml-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-4 d-md-flex align-items-center d-none d-sm-block">
                    <h3 class="mb-0">@lang('front.headings.payment') @lang('app.option') :</h3>
                    <img class="ml-2" src="{{asset('img/credit/visa.png')}}" />
                    <img class="ml-2" src="{{asset('img/credit/mastercard.png')}}" />
                    <img class="ml-2" src="{{asset('img/credit/american-express.png')}}" />
                </div>
                <div class="col-md-8">
                    <p class="mb-0">{!! $footer_setting->footer_text !!}</p>
                </div>
            </div>
        </div>
    </div>

</footer>
<!-- FOOTER END -->
