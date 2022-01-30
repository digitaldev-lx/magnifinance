<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\BookingTime;

class CreateBookingTimesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_times', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('multiple_booking', ['yes', 'no'])->default('yes');
            $table->integer('max_booking')->default(0);
            $table->enum('status', ['enabled', 'disabled'])->default('enabled');
            $table->integer('slot_duration')->default(30); // In minutes

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
        Schema::dropIfExists('booking_times');
    }

}
