<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultImageAndUpdateDescriptionToVendorPagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_pages', function (Blueprint $table) {
            $table->string('default_image')->nullable()->after('photos');
            $table->longText('description')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendor_pages', function (Blueprint $table) {
            $table->dropColumn('default_image');
        });
    }

}
