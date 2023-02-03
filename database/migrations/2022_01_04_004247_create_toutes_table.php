<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('toutes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

            $table->enum('ads_in_all_category', ['yes', 'no'])->default('no');
            $table->unsignedInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('article_id')->nullable();
            $table->foreign('article_id')->references('id')->on('articles')->onUpdate('cascade')->onDelete('cascade');

            $table->string('title');
            $table->text('image')->nullable();
            $table->text('description');
            $table->string('info1',40)->nullable();
            $table->string('info2',40)->nullable();
            $table->string('info3',40)->nullable();
            $table->double('price')->nullable();
            $table->string('call_to_action');
            $table->string('link');
            $table->date('from');
            $table->date('to');
            $table->double('amount');
            $table->double('avg_amount');
            $table->dateTime('paid_on')->nullable();
            $table->string('transaction_id')->unique()->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
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
        Schema::dropIfExists('toutes');
    }
}
