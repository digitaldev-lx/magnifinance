<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPointLatlongColumnRemoveLongitudeLatitudeColumnsVendorPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_pages', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
            $table->point('lat_long')->nullable()->after('map_option');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
