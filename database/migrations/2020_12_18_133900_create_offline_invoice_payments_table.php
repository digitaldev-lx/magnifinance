<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfflineInvoicePaymentsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offline_invoice_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('invoice_id')->references('id')->on('offline_invoices')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('offline_payment_methods')->onDelete('cascade')->onUpdate('cascade');

            $table->string('slip');
            $table->longText('description');
            $table->enum('status', ['pending', 'approve', 'reject']);

            $table->timestamps();
        });


        Schema::table('payments', function (Blueprint $table) {
            $table->bigInteger('offline_method_id')->unsigned()->nullable();
            $table->foreign('offline_method_id')->references('id')->on('offline_payment_methods')->onDelete('SET NULL')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offline_invoice_payments');
    }

}
