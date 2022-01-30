<?php

use App\CurrencyFormatSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencyFormatSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('currency_format_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('currency_position', ['left', 'right', 'left_with_space', 'right_with_space'])->default('right');
            $table->integer('no_of_decimal')->unsigned();
            $table->string('thousand_separator')->nullable();
            $table->string('decimal_separator')->nullable();
        });
        
        $currencyFormatSetting = new CurrencyFormatSetting();
        $currencyFormatSetting->currency_position = 'right';
        $currencyFormatSetting->no_of_decimal = '2';
        $currencyFormatSetting->thousand_separator = ',';
        $currencyFormatSetting->decimal_separator = '.';
        $currencyFormatSetting->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_format_settings');
    }

}
