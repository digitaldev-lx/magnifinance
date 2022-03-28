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
                        <span class="with-tax">
                            @if($service->tax_on_price_status == "active")
                                {!! ($service->discount > 0) ? "<s class='h6 text-danger'>".currencyFormatter($service->price,myCurrencySymbol())."</s> ".currencyFormatter(round($service->net_price * (1 + $service->taxServices[0]->tax->percent / 100)),myCurrencySymbol()) : currencyFormatter($service->net_price,myCurrencySymbol()) !!}
                            @else
                  é aqui que estou              {!! ($service->discount > 0) ? "<s class='h6 text-danger'>".currencyFormatter($service->net_price + $service->net_price * (1 + $service->taxServices[0]->tax->percent / 100),myCurrencySymbol())."</s> ".currencyFormatter(round($service->net_price * (1 + $service->taxServices[0]->tax->percent / 100)),myCurrencySymbol()) : currencyFormatter($service->net_price,myCurrencySymbol()) !!}
                            @endif
                        </span>

                        <span class="without-tax">
                            @if($service->tax_on_price_status == "active")
                                {!! ($service->discount > 0) ? "<s class='h6 text-danger'>".currencyFormatter($service->price - $service->price * ($service->taxServices[0]->tax->percent / 100) ,myCurrencySymbol())."</s> ".currencyFormatter($service->discounted_price - $service->discounted_price * ($service->taxServices[0]->tax->percent / 100),myCurrencySymbol()) : currencyFormatter($service->net_price - $service->net_price * ($service->taxServices[0]->tax->percent / 100),myCurrencySymbol()) !!}
                            @endif
                        </span>
                    </div>
                    <div class="card-footer p-1">
                        <a href="javascript:;"
                           data-service-price="{{ $service->discounted_price }}"
                           data-price-with-taxes="{{ $service->price_with_taxes }}"
                           data-service-id="{{ $service->id }}"
                           data-total_tax_percent="{{ $service->total_tax_percent }}"
                           data-service-name="{{ ucwords($service->name) }}"
                           data-tax-on-price-status="{{$service->tax_on_price_status}}"
                           class="btn btn-block btn-dark add-to-cart"><i class="fa fa-plus"></i> @lang('app.add')
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endforeach
