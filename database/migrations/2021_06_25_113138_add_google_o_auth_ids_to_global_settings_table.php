<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleOAuthIdsToGlobalSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->enum('google_calendar', ['active', 'deactive'])->default('deactive')->after('map_key');
            $table->text('google_client_id')->nullable()->after('google_calendar');
            $table->text('google_client_secret')->nullable()->after('google_client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->dropColumn('google_calendar');
            $table->dropColumn('google_client_id');
            $table->dropColumn('google_client_secret');
        });
    }

}
