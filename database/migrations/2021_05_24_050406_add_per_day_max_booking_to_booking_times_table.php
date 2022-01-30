<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerDayMaxBookingToBookingTimesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_times', function (Blueprint $table) {
            $table->integer('per_day_max_booking')->default(0)->after('max_booking');
            $table->integer('per_slot_max_booking')->default(0)->after('per_day_max_booking');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_times', function (Blueprint $table) {
            $table->dropColumn('per_day_max_booking');
            $table->dropColumn('per_slot_max_booking');
        });
    }

}
