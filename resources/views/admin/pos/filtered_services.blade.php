@foreach($categories as $category)
    <div class="row">
        @if($category->services->count() > 0)
        <div class="col-md-12 mt-2">
            <h5>{{ ucfirst($category->name) }}</h5>
        </div>
        @endif
        @foreach($category->services as $service)
            <div class="col-md-6 col-lg-3">
                <div class="card">
                    <img height="100em" class="card-img-top" src="{{ $service->service_image_url }}">
                    <div class="card-body p-2">
                        <p class="font-weight-normal">{{ ucwords($service->name) }}</p>
                        {!! ($service->discount > 0) ? "<s class='h6 text-danger'>".currencyFormatter($service->price,myCurrencySymbol())."</s> ".currencyFormatter($service->discounted_price,myCurrencySymbol()) : currencyFormatter($service->price,myCurrencySymbol()) !!}
                    </div>
                    <div class="card-footer p-1">
                        <a href="javascript:;"
                           data-service-price="{{ $service->discounted_price }}"
                           data-service-id="{{ $service->id }}"
                           data-total_tax_percent="{{ $service->total_tax_percent }}"
                           data-service-name="{{ ucwords($service->name) }}"
                           class="btn btn-block btn-dark add-to-cart"><i class="fa fa-plus"></i> @lang('app.add')
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endforeach
