<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUniversalSearchTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('universal_searches', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('location_id')->nullable();
            $table->string('searchable_id');
            $table->string('searchable_type');
            $table->string('title');
            $table->string('route_name');
            $table->unsignedInteger('count')->default(0)->nullable();
            $table->enum('type', ['frontend', 'backend'])->default('backend')->nullable();

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
        Schema::dropIfExists('universal_searches');
    }

}
