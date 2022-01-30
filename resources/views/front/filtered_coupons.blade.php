<div class="mt-4 d-flex flex-wrap">
    @foreach ($coupons as $coupon)
        <div class="col-lg-6 col-md-12 d-flex mb-4 mobile-no-padding">
            <div class="media coupon_box view_latest_coupon_box">
                <div class="coupon_discount align-self-center">
                <p class="mb-2">{{$coupon->company->company_name}}</p>
                    <h2 class="mb-2">{{$coupon->title}}</h2>
                </div>
                <div class="media-body coupon_code_box text-center position-relative">
                    <h6>
                        @if (!is_null($coupon->amount) && is_null($coupon->percent))
                        {{$coupon->amount}}
                    @elseif (is_null($coupon->amount) && !is_null($coupon->percent))
                        {{$coupon->amount}}%
                    @else
                        @lang('app.maxAmountOrPercent', ['percent' => $coupon->percent, 'maxAmount' => $coupon->amount])
                    @endif
                    <br><span>@lang('app.off')</span>
                    </h6>
                    <a href="javascript:;" id="coupon_one" class="show_latest_coupon_code show-coupon" data-coupon-id="{{$coupon->id}}" data-coupon-title="{{$coupon->title}}" data-coupon-description="{{$coupon->description}}" data-coupon-company="{{$coupon->company->company_name}}" data-coupon-code="{{$coupon->code}}" data-coupon-logo="{{$coupon->company->logo_url}}">Show Code</a>
                    <p class="mb-0"><i class="zmdi zmdi-time"></i>@lang('app.expireOn') {{  \Carbon\Carbon::parse($coupon->end_date_time)->translatedFormat($settings->date_format) }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="deals_pagination mt-4 d-flex justify-content-center" id="pagination">
    {{ $coupons->links() }}
</div>
