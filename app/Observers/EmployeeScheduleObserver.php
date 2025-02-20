<?php

namespace App\Observers;

use App\Models\EmployeeSchedule;
use App\Models\Role;

class EmployeeScheduleObserver
{

    public function creating(EmployeeSchedule $employeeSchedule)
    {
        $role = Role::where('name', 'customer')->withoutGlobalScopes()->first();

        if($role != 'customer') {

            if (company()) {

                $employeeSchedule->company_id = company()->id;
            }
        }
    }

}
