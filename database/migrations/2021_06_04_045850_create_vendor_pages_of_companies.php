<?php

use App\Company;
use App\VendorPage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorPagesOfCompanies extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $companies = Company::doesntHave('vendorPage')->get();
        
        foreach ($companies as $company) {
            VendorPage::create([
                'company_id' => $company->id,
                'address' => $company->address,
                'primary_contact' => $company->company_phone,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

}
