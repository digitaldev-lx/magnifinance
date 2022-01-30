<?php

use App\Role;
use Illuminate\Database\Migrations\Migration;

class AddNewRoleAgent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Role::create([
            'name' => 'agent',
            'display_name' => 'Agent',
            'description' => 'Agent',
        ]);
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
