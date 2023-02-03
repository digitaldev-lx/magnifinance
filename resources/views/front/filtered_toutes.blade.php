<div class="mt-4 d-flex flex-wrap">

@foreach($toutes as $tout)
<div class="row mt-3 adsContainer">
    <div class="col-lg-5 col-md-12">
        <div  id="deal_detail_slider">
            <div class="item">
                <div class="deal_detail_img position-relative">
                    <img src="{{ $tout->tout_image_url }}" data-src="{{ $tout->tout_image_url }}" alt="{{$tout->title}}" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7 col-md-12 deal_detail_box">
        <h2>{{ $tout->title }}</h2>
        <p>{{ $tout->description }}</p>
        {{--                    <a href="{{route('front.vendorPage',$service->company->slug)}}"><h3 class="mt-lg-1 mt-4">{{ $service->company->company_name }}</h3></a>--}}

        <div class="deal_detail_contact mt-1">
            <h6><i class="zmdi zmdi-dot-circle mr-2"></i>{{ $tout->info1 }}</h6>
            <h6>@if(!is_null($tout->info2))<i class="zmdi zmdi-dot-circle mr-2"></i>{{ $tout->info2 }}@endif</h6>
            <h6>@if(!is_null($tout->info3))<i class="zmdi zmdi-dot-circle mr-2"></i>{{ $tout->info3 }}@endif</h6>
        </div>
        @if($tout->price > 0)
        <div class="deal_detail_offer_with_price d-flex align-items-center">
            <p class="badge badge-danger text-white text-bold mt-0">{{ $tout->formated_price }}
        </div>
        @else
            <div class="heightPrice">
            </div>
        @endif
        <div class="deal_detail_expiry_date">
            <a href="{{$tout->link}}" class="btn btn-dark btn-block">{{ $tout->call_to_action }}</a>
        </div>

    </div>
</div>
@endforeach
</div>
