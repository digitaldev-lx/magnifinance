<?php

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::insert([
            [
                'language_code' => 'en',
                'language_name' => 'English',
                'status' => 'enabled',
            ],
            [
                'language_code' => 'pt',
                'language_name' => 'Portugues',
                'status' => 'enabled',
            ],
        ]);
    }

}
