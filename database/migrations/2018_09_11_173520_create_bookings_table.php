<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->foreign('coupon_id')->references('id')->on('coupons')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');

            $table->dateTime('date_time');
            $table->enum('status', ['pending', 'approved', 'in progress', 'completed', 'canceled'])->default('pending');
            $table->string('payment_gateway');
            $table->float('original_amount')->nullable();
            $table->float('product_amount')->nullable();
            $table->float('discount');
            $table->double('discount_percent');
            $table->double('coupon_discount')->nullable();
            $table->string('tax_name');
            $table->float('tax_percent', 8, 2);
            $table->float('tax_amount', 8, 2);
            $table->float('amount_to_pay');
            $table->enum('payment_status', ['pending', 'completed'])->default('completed');
            $table->string('source')->default('pos');
            $table->text('additional_notes')->nullable();

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
        Schema::dropIfExists('bookings');
    }

}
