<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMsg91ColumnToSmsSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->enum('msg91_status', ['active', 'deactive'])->default('deactive');
            $table->string('msg91_key')->nullable();
            $table->string('msg91_from')->nullable()->default('msgind');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sms_settings', function (Blueprint $table) {
            $table->dropColumn(['msg91_status','msg91_key','msg91_from']);
        });
    }

}
