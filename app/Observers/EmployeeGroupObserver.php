<?php

namespace App\Observers;

use App\EmployeeGroup;
use App\User;

class EmployeeGroupObserver
{

    public function deleting(EmployeeGroup $employeeGroup)
    {
        User::withoutGlobalScopes()->where('group_id', $employeeGroup->id)->update(['group_id' => null]);
    }

}
