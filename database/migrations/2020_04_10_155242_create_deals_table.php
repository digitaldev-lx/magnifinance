<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDealsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');

            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('deal_type');
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->time('open_time');
            $table->time('close_time');
            $table->integer('uses_limit')->nullable();
            $table->integer('used_time')->nullable();
            $table->double('original_amount')->nullable();
            $table->double('deal_amount')->nullable();
            $table->text('days')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive', 'expire'])->default('active');
            $table->text('description')->nullable();
            $table->string('discount_type');
            $table->integer('percentage')->nullable();
            $table->string('deal_applied_on');
            $table->integer('max_order_per_customer')->nullable();
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
        Schema::dropIfExists('deals');
    }

}
