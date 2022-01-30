<?php

use App\PackageModules;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageModulesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_modules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });

        $default_modules = array(
            array(
                'name' => 'Reports',
               ),
            array(
                'name' => 'POS',
            ),
            array(
                'name' => 'Employee Leave',
               ),
            array(
                'name' => 'Employee Schedule Setting',
               )
        );

        PackageModules::insert($default_modules);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_modules');
    }

}
