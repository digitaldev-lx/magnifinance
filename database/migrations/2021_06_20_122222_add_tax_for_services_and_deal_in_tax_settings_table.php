<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\BusinessService;
use App\Deal;
use App\ItemTax;
use App\Product;
use App\Tax;

class AddTaxForServicesAndDealInTaxSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tax = Tax::active()->first();
        $deals = Deal::withoutGlobalScopes(['company'])->get();
        $products = Product::withoutGlobalScopes(['company'])->get();
        $services = BusinessService::withoutGlobalScopes(['company'])->get();

        if ($services && $tax) {

            foreach ($services as $key => $service) {
                $serviceTax = new ItemTax();
                $serviceTax->tax_id = $tax->id;
                $serviceTax->service_id = $service->id;
                $serviceTax->deal_id = null;
                $serviceTax->product_id = null;
                $serviceTax->company_id = null;
                $serviceTax->save();
            }
        }

        if ($deals && $tax) {

            foreach ($deals as $deal) {
                $dealTax = new ItemTax();
                $dealTax->tax_id = $tax->id;
                $dealTax->service_id = null;
                $dealTax->deal_id = $deal->id;
                $dealTax->product_id = null;
                $dealTax->company_id = null;
                $dealTax->save();
            }
        }

        if ($products && $tax) {

            foreach ($products as $product) {
                $productTax = new ItemTax();
                $productTax->tax_id = $tax->id;
                $productTax->service_id = null;
                $productTax->deal_id = null;
                $productTax->product_id = $product->id;
                $productTax->company_id = null;
                $productTax->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

}
