<?php

namespace App\Observers;

use App\Helper\SearchLog;
use App\Product;
use Illuminate\Support\Facades\File;

class ProductObserver
{

    public function creating(Product $product)
    {
        if (company()) {
            $product->company_id = company()->id;
        }
    }

    /**
     * Handle the product "created" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        SearchLog::createSearchEntry($product->id, 'Product', $product->name, 'admin.products.edit', $product->company_id);
    }

    /**
     * Handle the product "updated" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        SearchLog::updateSearchEntry($product->id, 'Product', $product->name, 'admin.products.edit');
    }

    /**
     * Handle the product "deleted" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        SearchLog::deleteSearchEntry($product->id, 'admin.products.edit');

        // delete images folder from user-uploads/product directory
        File::deleteDirectory(public_path('user-uploads/product/'.$product->id));
    }

}
