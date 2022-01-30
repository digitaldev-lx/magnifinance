<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentGatewayCredentialTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_gateway_credentials', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->enum('offline_payment', [0,1])->default(1);
            $table->enum('show_payment_options', ['hide', 'show'])->default('show');
            $table->string('paypal_client_id')->nullable();
            $table->string('paypal_secret')->nullable();
            $table->enum('paypal_mode', ['sandbox', 'live'])->default('sandbox');
            $table->enum('paypal_status', ['active', 'deactive'])->default('deactive');
            $table->string('stripe_client_id')->nullable()->default(null);
            $table->string('stripe_secret')->nullable()->default(null);
            $table->string('stripe_webhook_secret')->nullable()->default(null);
            $table->enum('stripe_status', ['active', 'deactive'])->default('deactive');
            $table->enum('stripe_commission_status', ['active', 'deactive'])->default('deactive');
            $table->integer('stripe_commission_percentage')->nullable()->default(null);
            $table->string('razorpay_key')->nullable();
            $table->string('razorpay_secret')->nullable();
            $table->string('razorpay_webhook_secret')->nullable();
            $table->enum('razorpay_status', ['active', 'deactive'])->default('deactive');
            $table->enum('razorpay_commission_status', ['active', 'deactive'])->default('deactive');
            $table->integer('razorpay_commission_percentage')->nullable()->default(null);

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
        Schema::dropIfExists('payment_gateway_credentials');
    }

}
