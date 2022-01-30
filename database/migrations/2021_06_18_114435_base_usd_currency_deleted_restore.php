<?php

use App\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BaseUsdCurrencyDeletedRestore extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Currency::count() > 0) {
            $baseCurrency = Currency::withTrashed()->where('currency_code', 'USD')->first();

            if ($baseCurrency) {
                $baseCurrency->exchange_rate = 1;
                $baseCurrency->save();
                $baseCurrency->trashed() ? $baseCurrency->restore() : '';
            }
            else{
                Currency::create(['currency_name' => 'US Dollars', 'currency_code' => 'USD', 'currency_symbol' => '$', 'exchange_rate' => 1]);
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
