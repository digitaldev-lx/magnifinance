<?php

use App\Models\Company;
use App\Models\Role;
use App\Models\VendorPage;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CompanySeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $company = Company::create(
        [
            'package_id' => 6,
            'company_name' => 'Centro Ana Garcia',
            'company_email' => 'geral@centroanagarcia.com',
            'company_phone' => '00351963958684',
            'address' => 'Rua dos Arneiros 21C, 1500-055 Lisboa',
            'date_format' => 'd-m-Y',
            'time_format' => 'h:i a',
            'logo' => 'company.png',
            'website' => 'http://www.centroanagarcia.com',
            'timezone' => 'Europe/Lisbon',
            'currency_id' => '1',
            'locale' => 'en',
            'status' => 'active',
            'verified' => 'yes',
            'popular_store' => '1'
        ]);

        $vendorPage = VendorPage::where('company_id', $company->id)->first();
        $vendorPage->description = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
        $vendorPage->save();
        $path = base_path('public/' . 'user-uploads' . '/company-logo/');

        if (!File::isDirectory($path)) {
            File::makeDirectory($path);
        }

        File::copy(public_path('front/images/company.png'), public_path('user-uploads/company-logo/company.png'));

        $adminRole1 = Role::select('id', 'name')->where(['name' => 'administrator', 'company_id' => $company->id])->first();
        $employeeRole1 = Role::select('id', 'name')->where(['name' => 'employee', 'company_id' => $company->id])->first();

        // Insert admin
        $admin1 = User::create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => '123456',
            'company_id' => 1,
        ]);
        $admin1->attachRole($adminRole1->id);

        // Insert employees
        $employee1 = new User();
        $employee1->name = 'Julia Dolinga';
        $employee1->email = 'jdolinga@centroanagarcia.com';
        $employee1->password = '123456';
        $employee1->mobile = '00351925412626';
        $employee1->company_id = $company->id;
        $employee1->save();

        // add default employee role
        $employee1->attachRole($employeeRole1->id);

    }

}
