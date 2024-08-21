<?php

namespace App\Observers;

use App\Models\Role;

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
