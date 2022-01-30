<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChooseOptionsColumnsInGoogleCaptchaSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::table('google_captcha_settings', function (Blueprint $table) {
            $table->enum('login_page', ['active', 'inactive'])->default('inactive')->after('v3_secret_key');
            $table->enum('customer_page', ['active', 'inactive'])->default('inactive')->after('login_page');
            $table->enum('vendor_page', ['active', 'inactive'])->default('inactive')->after('customer_page');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('google_captcha_settings', function (Blueprint $table) {
            //
        });
    }

}
