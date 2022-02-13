<div class="mt-4 d-flex flex-wrap">
    @foreach ($services as $service)
        <div class="col-md-6 mobile-no-padding">
            <div class="card single_deal_box border-0">
                <div class="card-image position-relative">
                    <a href="{{$service->service_detail_url}}">
                        <img class="card-img-top" src="{{asset('front/images/pixel.gif')}}" data-src="{{ $service->service_image_url }}" alt="Card image" >
                    </a>
                    @if($service->discount > 0)
                    <span>
                        @if($service->discount_type == 'fixed')
                            {{ currencyFormatter($service->discount) }}
                        @endif

                        @if($service->discount_type == 'percent')
                            {{$service->discount}} %
                        @endif
                        @lang('app.off')
                    </span>
                    @endif
                </div>
                <div class="card-body all_deals_services">
                    <a href="{{ $service->service_detail_url }}">
                        <h4 class="card-title">{{ ucwords($service->name) }}</h4>
                    </a>
                    <p class="card-text">
                        <span class="deal_card_current_price">{{ $service->formated_discounted_price }}</span>
                        @if($service->discount > 0)
                        <span class="deal_card_old_price">{{ $service->formated_price }}</span>|
                        @else &nbsp;&nbsp;|
                        @endif
                        <span class="deal_card_name">{{ $service->company->company_name }}</span>|
                        <span class="deal_card_location">{{ $service->location->name }}</span>
                        @if ($service->ratings->count() > 0 && $settings->rating_status == 'active')
                            | &nbsp;&nbsp;
                            <i class="zmdi @if($service->ratings->sum('rating')/$service->ratings->count() >= 1) zmdi-star @else zmdi-star-outline @endif align-self-center"></i>
                            <i class="zmdi @if($service->ratings->sum('rating')/$service->ratings->count() >= 2) zmdi-star @else zmdi-star-outline @endif align-self-center"></i>
                            <i class="zmdi @if($service->ratings->sum('rating')/$service->ratings->count() >= 3) zmdi-star @else zmdi-star-outline @endif align-self-center"></i>
                            <i class="zmdi @if($service->ratings->sum('rating')/$service->ratings->count() >= 4) zmdi-star @else zmdi-star-outline @endif align-self-center"></i>
                            <i class="zmdi @if($service->ratings->sum('rating')/$service->ratings->count() >= 5) zmdi-star @else zmdi-star-outline @endif align-self-center"></i>
                            ({{ $service->ratings->count() }})
                        @endif
                    </p>
                    <a
                        id="service{{ $service->id }}"
                        href="javascript:;"
                        class="btn w-100 add-to-cart"
                        data-type="service"
                        data-unique-id="{{ $service->id }}"
                        data-id="{{ $service->id }}"
                        data-price="{{$service->converted_discounted_price}}"
                        data-name="{{ ucwords($service->name) }}"
                        data-company-id="{{ $service->company->id }}"
                        aria-expanded="false">
                        @lang('front.addToCart')
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="deals_pagination mt-4 d-flex justify-content-center" id="pagination">
    {!! $services->links() !!}
</div>
