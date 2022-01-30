<?php

use App\FrontThemeSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToFrontThemeSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('front_theme_settings', function (Blueprint $table) {
            $table->string('title');
            $table->string('favicon')->nullable();
            $table->string('customJS');
        });

        $theme = FrontThemeSetting::first();
        
        if($theme){
            $theme->title      = 'Appointo Multi Vendor';
            $theme->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('front_theme_settings', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('favicon');
        });
    }

}
