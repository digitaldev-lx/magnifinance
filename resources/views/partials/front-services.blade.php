<div class="row">
    <div class="col-md-8">
        <div class="row p-2" id="serviceArea">

            @forelse ($headerCategories as $services)
              @foreach ($services->services()->groupBy('name')->get() as $service)
              <div class="col-md-4">
                <ul class="nav flex-column categories_sub_sub_menu">
                  <li class="nav-item">
                    <a class="nav-link active" href="
                        {{ route('front.search', ['l' => $service->location_id, 'q' => $service->name]) }} ">
                        <span></span> {{ $service->name }}
                    </a>
                  </li>
                </ul>
              </div>
              @endforeach
            @empty
                <div class="col-md-12 no-data"><h6> @lang('front.serviceNotFound')..! </h6></div>
            @endforelse

        </div>
    </div>

    <div class="col-md-4" id="serviceImageArea">
        @php
            $srcAttr = $headerCategories->count() != 1 ? '' : $headerCategories->first()->category_image_url;
        @endphp
        <img class="img-fluid m-2" src="{{asset('front/images/pixel.gif')}}" data-src="{{$srcAttr}}" alt="">
    </div>
</div>
