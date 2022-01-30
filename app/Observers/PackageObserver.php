<?php

namespace App\Observers;

use App\Company;
use App\GlobalSetting;
use App\ModuleSetting;
use App\Package;

class PackageObserver
{

    /**
     * Handle the package "created" event.
     *
     * @param  \App\Package  $package
     * @return void
     */
    public function creating(Package $package)
    {
        $currency = GlobalSetting::first();

        if($currency){
            $package->currency_id = $currency->currency_id;
        }
    }

    /**
     * Handle the package "updated" event.
     *
     * @param  \App\Package  $package
     * @return void
     */
    public function updated(Package $package)
    {
        if($package->isDirty('package_modules'))
        {
            /* Remove all entries of companies from package_setting which has been assigned to particular package */
            $companies = Company::where('package_id', $package->id)->get();

            foreach ($companies as $key => $company) {
                ModuleSetting::where('company_id', $company->id)->delete();
            }

            /* Assign new/updated modules to companies */
            $arr = json_decode($package->package_modules, true);

            if(!is_null($arr)) {
                foreach ($companies as $key => $company) {
                    foreach($arr as $module) {
                        $admin_data = [
                            'company_id' => $company->id,
                            'module_name' => $module,
                            'status' => 'active',
                            'type' => 'administrator'
                        ];
                        $employee_data = [
                            'company_id' => $company->id,
                            'module_name' => $module,
                            'status' => 'active',
                            'type' => 'employee'
                        ];
                        ModuleSetting::create($admin_data);
                        ModuleSetting::create($employee_data);
                    }
                }
            }
        }

        /* end of is_Dirty() */
    }

    /* end of updated */

}
