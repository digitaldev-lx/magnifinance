<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeGroupServicesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_group_services', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('employee_groups_id')->nullable();
            $table->unsignedInteger('business_service_id')->nullable();

            $table->foreign('employee_groups_id')->references('id')->on('employee_groups')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('business_service_id')->references('id')->on('business_services')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_group_services');
    }

}
