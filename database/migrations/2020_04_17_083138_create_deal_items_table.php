<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDealItemsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deal_items', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('deal_id')->nullable();
            $table->foreign('deal_id')->references('id')->on('deals')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('business_service_id')->nullable();
            $table->foreign('business_service_id')->references('id')->on('business_services')->onUpdate('cascade')->onDelete('cascade');

            $table->tinyInteger('quantity');
            $table->double('unit_price');
            $table->double('discount_amount');
            $table->double('total_amount');

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
        Schema::dropIfExists('deal_items');
    }

}
