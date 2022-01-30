<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CurrencySeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(PackageSeeder::class);
        $this->call(GlobalSettingSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(SuperAdminSettingSeeder::class);
        $this->call(DefaultSuperAdminSeeder::class);
        $this->call(LocationSeeder::class);

        $this->call(CompanySeeder::class);
        $this->call(PaymentSeeder::class);
        $this->call(EmployeeScheduleSeeder::class);
        $this->call(frontSliderSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(DealSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(BookingSeeder::class);
    }

}
