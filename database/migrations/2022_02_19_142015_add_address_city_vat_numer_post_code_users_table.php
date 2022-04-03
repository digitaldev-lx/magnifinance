<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressCityVatNumerPostCodeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('vat_number')->default("999999990")->after('email');
            $table->text('address')->nullable()->after('mobile');
            $table->string('city')->nullable()->after('mobile');
            $table->string('post_code')->default("1000-001")->after("mobile");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['vat_number', 'address', 'city', 'post_code']);
        });
    }
}
