<?php

use App\Package;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('currency_id')->nullable()->default(null);
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete(null)->onUpdate('cascade');

            $table->string('name');
            $table->bigInteger('max_employees');
            $table->bigInteger('max_services');
            $table->bigInteger('max_deals');
            $table->bigInteger('max_roles');
            $table->bigInteger('no_of_days');
            $table->bigInteger('notify_before_days');
            $table->string('trial_message');
            $table->float('monthly_price', 20, 3);
            $table->float('annual_price', 20, 3);
            $table->string('stripe_monthly_plan_id')->nullable();
            $table->string('stripe_annual_plan_id')->nullable();
            $table->string('razorpay_monthly_plan_id')->nullable();
            $table->string('razorpay_annual_plan_id')->nullable();
            $table->text('package_modules')->nullable();
            $table->longText('description')->nullable();
            $table->string('type')->nullable();
            $table->enum('make_private', ['true', 'false'])->default('false');
            $table->enum('mark_recommended', ['true', 'false'])->default('false');
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }

}
