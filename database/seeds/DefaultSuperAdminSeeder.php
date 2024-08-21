<?php

use App\Models\PaymentGatewayCredentials;
use App\Models\Role;
use App\Models\Tax;
use App\User;
use Illuminate\Database\Seeder;

class DefaultSuperAdminSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'Super Admin';
        $user->email = 'superadmin@example.com';
        $user->calling_code = '+91';
        $user->mobile = '1919191919';
        $user->password = '123456';

        $user->save();

        $user->attachRole(Role::select('id', 'name')->where('name', 'superadmin')->first()->id);

        // Add default payment credentials
        PaymentGatewayCredentials::insert([
                'company_id' => null,
        ]);

        // seed tax setting
        Tax::create([
            'name' => 'IVA',
            'percent' => 23,
            'status' => 'active',
        ]);

    }

}
