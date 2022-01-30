<div class="row">
    @foreach ($products as $product)
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <img height="100em" class="card-img-top" src="{{ $product->product_image_url }}">
                <div class="card-body p-2">
                    <p class="font-weight-normal">{{ ucwords($product->name) }}</p>
                    {!! $product->discount > 0 ? "<s class='h6 text-danger'>" . currencyFormatter(
                        $product->price,myCurrencySymbol()) . '</s> ' .  currencyFormatter($product->discounted_price,myCurrencySymbol()) :
                    currencyFormatter($product->price,myCurrencySymbol()) !!}
                </div>
                <div class="card-footer p-1">
                    <a href="javascript:;"
                        data-product-price="{{ $product->discounted_price }}"
                        data-product-id="{{ $product->id }}"
                        data-total_tax_percent="{{ $product->total_tax_percent }}"
                        data-product-name="{{ ucwords($product->name) }}"
                        class="btn btn-block btn-dark add-to-cart"><i class="fa fa-plus"></i> @lang('app.add')
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
