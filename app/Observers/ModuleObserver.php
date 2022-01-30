<?php

namespace App\Observers;

use App\Helper\Permissions;
use App\Module;
use App\Role;

class ModuleObserver
{

    public function created(Module $module)
    {
        Permissions::createPermissions($module);

        $default_roles = config('laratrust_seeder.default_roles');

        foreach ($default_roles as $default_role) {
            $roles = Role::select('id', 'name')->withoutGlobalScopes()->where('name', $default_role)->get();

            if ($roles->count() > 0) {
                foreach ($roles as $role) {
                    Permissions::assignPermissions($role);
                }
            }
        }
    }

}
