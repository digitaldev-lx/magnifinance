<?php

namespace App\Observers;

use App\Helper\SearchLog;
use App\Role;
use App\User;

class RoleObserver
{

    public function saving(Role $role)
    {
        if($role->company_id){
            $role->company_id = $role->company_id;
        }
        elseif (company()) {
            $role->company_id = company()->id;
        }
    }

}
