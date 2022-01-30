<?php

use App\GoogleCaptchaSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleCaptchaSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_captcha_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('status', ['active', 'deactive'])->default('deactive');

            $table->enum('v2_status', ['active', 'deactive'])->default('deactive');
            $table->string('v2_site_key')->nullable();
            $table->string('v2_secret_key')->nullable();

            $table->enum('v3_status', ['active', 'deactive'])->default('deactive');
            $table->string('v3_site_key')->nullable();
            $table->string('v3_secret_key')->nullable();

            $table->timestamps();
        });

        $captcha = new GoogleCaptchaSetting();
        $captcha->status = 'deactive';
        $captcha->save();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('google_captcha_settings');
    }

}
