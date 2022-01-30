<?php

use App\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $config = config('laratrust_seeder.modules');

        $modulesArr = array_keys($config);

        foreach ($modulesArr as $module) {
            $reqModule = new Module();

            $reqModule->name = $module;
            $reqModule->display_name = ucwords(str_replace('_', ' ', $module));
            $reqModule->description = 'modules.module.'.lcfirst(implode('', explode(' ', ucwords(str_replace('_', ' ', $module.'_description')))));

            $reqModule->save();
        }
    }

}
