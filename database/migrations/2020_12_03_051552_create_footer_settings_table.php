<?php

use App\FooterSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFooterSettingsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('footer_settings', function (Blueprint $table) {
            $table->id();
            $table->text('social_links')->nullable();
            $table->string('footer_text');
            $table->timestamps();
        });

        $data = [
            'social_links' => [
                [
                    'name' => 'facebook',
                    'link' => 'https://facebook.com'
                ],
                [
                    'name' => 'twitter',
                    'link' => 'https://twitter.com'
                ],
                [
                    'name' => 'instagram',
                    'link' => 'https://instagram.com'
                ],
                [
                    'name' => 'pinterest',
                    'link' => 'https://pinterest.com'
                ],
                [
                    'name' => 'linkedin',
                    'link' => 'https://linkedin.com'
                ],
            ],
            'footer_text' => 'Froiden Technologies Pvt. Ltd. © 2020 - 2025 All Rights Reserved.'
        ];

        FooterSetting::create($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('footer_settings');
    }

}
