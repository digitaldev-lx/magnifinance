<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('location_id')->default(1);
            $table->foreign('location_id')->references('id')->on('locations');
            $table->unsignedInteger('company_id')->default(1);
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('name');
            $table->text('description');
            $table->float('price');
            $table->float('discount');
            $table->enum('discount_type', ['percent', 'fixed']);
            $table->string('image')->nullable();
            $table->string('default_image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('products');
    }

}
