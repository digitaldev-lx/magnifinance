<?php

use App\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $package = new Package();
        $package->name = 'Default';
        $package->status = 'active';
        $package->type = 'default';
        $package->currency_id = 1;
        $package->save();

        $package = new Package();
        $package->name = 'Trial';
        $package->max_employees = 5;
        $package->max_services = 5;
        $package->max_deals = 5;
        $package->max_roles = 5;
        $package->description = 'Lorem ipsum kjvfds ds sd  ads sad v adsv ads vsd v sd s f aewg reb dfb';
        $package->status = 'active';
        $package->type = 'trial';
        $package->no_of_days = '0';
        $package->notify_before_days = '0';
        $package->package_modules = '{"1":"Reports","2":"POS","3":"Employee Leave","4":"Employee Schedule"}';
        $package->currency_id = 1;
        $package->save();

        $package = new Package();
        $package->name = 'Free';
        $package->max_employees = 5;
        $package->max_services = 10;
        $package->max_deals = 2;
        $package->max_roles = 3;
        $package->description = 'Lorem ipsum kjvfds ds sd  ads sad v adsv ads vsd v sd s f aewg reb dfb';
        $package->make_private = 'false';
        $package->mark_recommended = 'false';
        $package->status = 'active';
        $package->package_modules = '{"1":"Reports","2":"Employee Schedule"}';
        $package->currency_id = 1;
        $package->save();

        $package = new Package();
        $package->name = 'Starter';
        $package->max_employees = 3;
        $package->max_services = 7;
        $package->max_deals = 5;
        $package->max_roles = 10;
        $package->monthly_price = 50;
        $package->annual_price = 500;
        $package->description = 'Lorem ipsum kjvfds ds sd  ads sad v adsv ads vsd v sd s f aewg reb dfb';
        $package->make_private = 'false';
        $package->mark_recommended = 'false';
        $package->status = 'active';
        $package->package_modules = '{"1":"POS","2":"Employee Leave","3":"Employee Schedule"}';
        $package->currency_id = 1;
        $package->save();

        $package = new Package();
        $package->name = 'Medium';
        $package->max_employees = 20;
        $package->max_services = 10;
        $package->max_deals = 15;
        $package->max_roles = 20;
        $package->monthly_price = 100;
        $package->annual_price = 1000;
        $package->description = 'Lorem ipsum kjvfds ds sd  ads sad v adsv ads vsd v sd s f aewg reb dfb';
        $package->make_private = 'false';
        $package->mark_recommended = 'true';
        $package->status = 'active';
        $package->package_modules = '{"1":"Reports","2":"POS","3":"Employee Leave","4":"Employee Schedule"}';
        $package->currency_id = 1;
        $package->save();

        $package = new Package();
        $package->name = 'Larger';
        $package->max_employees = 15;
        $package->max_services = 40;
        $package->max_deals = 20;
        $package->max_roles = 30;
        $package->monthly_price = 500;
        $package->annual_price = 5000;
        $package->description = 'Lorem ipsum kjvfds ds sd  ads sad v adsv ads vsd v sd s f aewg reb dfb';
        $package->make_private = 'false';
        $package->mark_recommended = 'false';
        $package->status = 'active';
        $package->package_modules = '{"1":"Reports","2":"POS","3":"Employee Leave","4":"Employee Schedule","5":"Google Calendar"}';
        $package->currency_id = 1;
        $package->save();
    }

}
