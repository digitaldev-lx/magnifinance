<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanySettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('currency_id')->nullable();
            $table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('cascade');

            $table->string('company_name');
            $table->string('company_email');
            $table->string('company_phone');
            $table->string('logo')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('country');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('h:i A');
            $table->string('website')->nullable();
            $table->string('timezone');
            $table->string('locale');
            $table->point('lat_long')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->dateTime('licence_expire_on')->nullable();
            $table->enum('popular_store', [0, 1])->default(0)->nullable();

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
        Schema::dropIfExists('companies');
    }

}
