<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGlobalSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_settings', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('cascade');

            $table->string('company_name');
            $table->string('company_email');
            $table->string('company_phone');
            $table->string('contact_email');
            $table->string('logo')->nullable();
            $table->text('address');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('h:i A');
            $table->string('website');
            $table->string('timezone');
            $table->string('locale');
            $table->string('sign_up_note');
            $table->string('terms_note');
            $table->string('purchase_code', 100)->nullable();
            $table->timestamp('supported_until')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_settings');
    }

}
