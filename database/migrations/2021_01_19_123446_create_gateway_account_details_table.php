<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGatewayAccountDetailsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gateway_account_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('account_id', 100);
            $table->string('connection_status', 50)->default('not_connected'); // used to know the status of connection between superadmin and admins gateways
            $table->enum('account_status', ['active', 'inactive'])->default('inactive'); // used in case of multiple accounts for same gateway to identify which one is active
            $table->string('gateway', 50);
            $table->string('link')->nullable();
            $table->dateTime('link_expire_at')->nullable();
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
        Schema::dropIfExists('gateway_account_details');
    }

}
