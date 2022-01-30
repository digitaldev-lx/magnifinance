<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Add5ColumnsToCompanySettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->enum('disable_slot', ['enabled', 'disabled'])->default('disabled')->after('licence_expire_on');
            $table->enum('booking_time_type', ['sum', 'avg', 'max', 'min'])->after('disable_slot');
            $table->string('booking_per_day')->nullable()->after('booking_time_type');
            $table->enum('employee_selection', ['enabled', 'disabled'])->default('disabled')->after('booking_per_day');
            $table->string('multi_task_user')->nullable()->after('employee_selection');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('disable_slot');
            $table->dropColumn('booking_time_type');
            $table->dropColumn('booking_per_day');
            $table->dropColumn('employee_selection');
            $table->dropColumn(['multi_task_user']);
        });
    }

}
