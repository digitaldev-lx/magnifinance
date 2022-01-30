<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image')->nullable();
            $table->enum('have_content', ['yes', 'no'])->default('no');
            $table->longText('content')->nullable();
            $table->string('action_button')->nullable();
            $table->string('url')->nullable();
            $table->enum('open_tab', ['current', 'new'])->default('current')->nullable();
            $table->enum('content_alignment', ['left', 'right'])->default('left')->nullable();
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
        Schema::dropIfExists('media');
    }

}
