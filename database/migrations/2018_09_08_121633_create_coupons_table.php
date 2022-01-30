<?php

use App\Module;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title');
            $table->dateTime('start_date_time')->nullable();
            $table->dateTime('end_date_time')->nullable();
            $table->integer('uses_limit')->nullable();
            $table->integer('used_time')->nullable();
            $table->double('amount')->nullable();
            $table->enum('discount_type', ['percentage', 'amount'])->default('percentage')->nullable();
            $table->integer('minimum_purchase_amount')->default(0);
            $table->text('days')->nullable();
            $table->enum('status', ['active', 'inactive', 'expire'])->default('active');
            $table->text('description')->nullable();

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
        Schema::dropIfExists('coupons');
    }

}
