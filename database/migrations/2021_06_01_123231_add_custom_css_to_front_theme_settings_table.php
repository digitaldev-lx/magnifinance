<?php

use App\FrontThemeSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomCssToFrontThemeSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $frontThemeSetting = FrontThemeSetting::first();

        if ($frontThemeSetting) {
            $frontThemeSetting->custom_css = $frontThemeSetting->custom_css .'
        /* Coupon Box */
.coupon_code_box a {
    background-color: #ffcc00;
}
/* Deals Flag */
.featuredDealDetail .tag {
    background-color: #ffcc00;
}
/* Cart itme quantity number */
.cart-badge {
    background-color: #f72222;
}';

            $frontThemeSetting->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

}
