<?php

use App\Module;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTicketModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        $module = Module::count();

        if ($module > 0) {
            $config = [
                'ticket' => [
                    'superadmin' => 'c,r,u,d',
                    'administrator' => 'c,r,u,d'
                ]
            ];

            $modulesArr = array_keys($config);

            foreach ($modulesArr as $module) {
                $reqModule = new Module();

                $reqModule->name = $module;
                $reqModule->display_name = ucwords(str_replace('_', ' ', $module));
                $reqModule->description = 'modules.module.' . lcfirst(implode('', explode(' ', ucwords(str_replace('_', ' ', $module . '_description')))));

                $reqModule->save();
            }
        }
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
