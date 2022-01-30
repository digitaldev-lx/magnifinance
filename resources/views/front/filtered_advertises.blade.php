<div class="mt-4 d-flex flex-wrap">

@foreach($advertises as $advertise)
<div class="row mt-3 adsContainer">
    <div class="col-lg-5 col-md-12">
        <div  id="deal_detail_slider">
            <div class="item">
                <div class="deal_detail_img position-relative">
                    <img src="{{ $advertise->advertise_image_url }}" data-src="{{ $advertise->advertise_image_url }}" alt="{{$advertise->title}}" />
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7 col-md-12 deal_detail_box">
        <h2>{{ $advertise->title }}</h2>
        <p>{{ $advertise->description }}</p>
        {{--                    <a href="{{route('front.vendorPage',$service->company->slug)}}"><h3 class="mt-lg-1 mt-4">{{ $service->company->company_name }}</h3></a>--}}

        <div class="deal_detail_contact mt-1">
            <h6><i class="zmdi zmdi-dot-circle mr-2"></i>{{ $advertise->info1 }}</h6>
            <h6>@if(!is_null($advertise->info2))<i class="zmdi zmdi-dot-circle mr-2"></i>{{ $advertise->info2 }}@endif</h6>
            <h6>@if(!is_null($advertise->info3))<i class="zmdi zmdi-dot-circle mr-2"></i>{{ $advertise->info3 }}@endif</h6>
        </div>
        @if($advertise->price > 0)
        <div class="deal_detail_offer_with_price d-flex align-items-center">
            <p class="badge badge-danger text-white text-bold mt-0">{{ $advertise->formated_price }}
        </div>
        @else
            <div class="heightPrice">
            </div>
        @endif
        <div class="deal_detail_expiry_date">
            <a href="{{$advertise->link}}" class="btn btn-dark btn-block">{{ $advertise->call_to_action }}</a>
        </div>

    </div>
</div>
@endforeach
</div>
