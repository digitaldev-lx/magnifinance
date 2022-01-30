<?php

use Illuminate\Database\Seeder;
use App\Currency;

class CurrencySeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currency = new Currency();
        $currency->currency_name = 'Euro';
        $currency->currency_symbol = '€';
        $currency->currency_code = 'EUR';
        $currency->exchange_rate = 1.13;
        $currency->save();

        $currency = new Currency();
        $currency->currency_name = 'US Dollars';
        $currency->currency_code = 'USD';
        $currency->currency_symbol = '$';
        $currency->exchange_rate = 1;
        $currency->save();
    }

}
