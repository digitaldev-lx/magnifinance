<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\SocialAuthSetting;

class CreateSocialAuthSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('social_auth_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('google_client_id')->nullable();
            $table->string('google_secret_id')->nullable();
            $table->enum('google_status', ['active', 'inactive'])->default('inactive');
            $table->string('facebook_client_id')->nullable();
            $table->string('facebook_secret_id')->nullable();
            $table->enum('facebook_status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
        });

        Schema::create('socials', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->text('social_id');
            $table->text('social_service');
            $table->timestamps();
        });

        $socialAuth = new SocialAuthSetting();
        $socialAuth->google_status = 'inactive';
        $socialAuth->facebook_status = 'inactive';
        $socialAuth->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_auth_settings');
    }

}
