<?php

use App\PackageModules;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleCalendarPackageModule extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $default_modules = array(
            array( 'name' => 'Google Calendar', )
        );

        PackageModules::insert($default_modules);

        $packageModule = PackageModules::where('name', 'Employee Schedule Setting')->first();

        if ($packageModule) {
            $packageModule->update(['name' => 'Employee Schedule']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

}
