<?php

use App\Section;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('status', ['active', 'inactive'])->default('active');
        });

        $sections = [
            [
                'name' => 'Slider Section'
            ],
            [
                'name' => 'Deal Section'
            ],
            [
                'name' => 'Category Section'
            ],
            [
                'name' => 'Coupon Section'
            ],
            [
                'name' => 'Spotlight Section'
            ],
        ];

        Section::insert($sections);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sections');
    }

}
