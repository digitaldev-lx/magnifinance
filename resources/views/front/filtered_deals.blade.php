<div class="mt-4 d-flex flex-wrap">
    @foreach ($deals as $deal)
        <div class="col-md-6 mobile-no-padding">
            <div class="card single_deal_box border-0">
                <div class="card-image position-relative">
                    <a class="m-auto" href="{{$deal->deal_detail_url}}">
                    <img class="card-img-top" src="{{asset('front/images/pixel.gif')}}" data-src="{{ $deal->deal_image_url }}" alt="Card image"></a>
                    @if($deal->percentage > 0)
                    <span>
                        @if($deal->discount_type == 'percentage')
                            {{$deal->percentage}}%
                        @else
                        {{currencyFormatter($deal->converted_original_amount - $deal->converted_deal_amount)}}
                        @endif
                        @lang('app.off')
                    </span>
                    @endif
                </div>
                <div class="card-body all_deals_services">
                    <h4 class="card-title">{{ $deal->title }}</h4>
                    <p class="card-text">
                        <span class="deal_card_current_price">{{ $deal->formated_deal_amount }}</span>
                        @if($deal->percentage > 0)
                        <span class="deal_card_old_price">{{ $deal->formated_original_amount }}</span>|
                        @else &nbsp;&nbsp;|
                        @endif
                        <span class="deal_card_name">{{ $deal->company->company_name }} </span>|
                        <span class="deal_card_location">{{ $deal->location->name }}</span>
                    </p>
                    <a
                        id="deal{{ $deal->id }}"
                        href="javascript:;"
                        class="btn w-100 add-to-cart"
                        data-type="deal"
                        data-unique-id="deal{{ $deal->id }}"
                        data-id="{{ $deal->id }}"
                        data-price="{{$deal->converted_deal_amount}}"
                        data-name="{{ ucwords($deal->title) }}"
                        data-company-id="{{ $deal->company->id }}"
                        data-max-order="{{ $deal->max_order_per_customer }}"
                        aria-expanded="false">
                        @lang('front.addToCart')
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="deals_pagination mt-4 d-flex justify-content-center" id="pagination">
    {{ $deals->links() }}
</div>
