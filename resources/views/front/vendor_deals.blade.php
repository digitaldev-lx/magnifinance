<div class="mt-4 d-flex flex-wrap">
    @foreach ($deals as $deal)
        <div class="col-md-6">
            <div class="media">
                <div class="featured_deal_imgBox">
                    <a class="m-auto" href="{{ $deal->deal_detail_url }}">
                        <img src="{{asset('front/images/pixel.gif')}}" data-src="{{ $deal->deal_image_url }}" alt="{{ $deal->title }}"></a>
                </div>
                <div class="media-body featuredDealDetail position-relative">
                    <span class="tag">{{ $deal->deal_type == '' ? __('app.offer') : __('app.combo') }}</span>
                    <a  class="featuredHeading" href="{{route('front.vendorPage',$deal->company->slug)}}">{{ $deal->company->company_name }}</a>
                    <h1>{{ $deal->title }}</h1>
                    <p class="mb-lg-1 mb-xl-3">{{ $deal->formated_deal_amount }}
                        &nbsp;&nbsp;<span>{{ $deal->formated_original_amount }}</span>
                    </p>
                    <a id="deal{{ $deal->id }}"
                        href="javascript:;"
                        class="btn w-100 add-to-cart"
                        data-type="deal"
                        data-unique-id="deal{{ $deal->id }}"
                        data-id="{{ $deal->id }}"
                        data-price="{{ $deal->converted_deal_amount }}"
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
