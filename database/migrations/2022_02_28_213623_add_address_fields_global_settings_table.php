<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressFieldsGlobalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('global_settings', function (Blueprint $table) {
            $table->string('city')->nullable()->after('address');
            $table->string('post_code')->default("0000-000")->after("address");
            $table->unsignedInteger('country_id')->nullable()->after('address');
            $table->foreign('country_id')->references('id')->on('countries')->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropColumn(['city', 'post_code', 'country_id']);
        });
    }
}
