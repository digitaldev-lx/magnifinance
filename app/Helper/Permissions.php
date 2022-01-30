<?php

namespace App\Helper;

use App\Permission;
use App\Role;
use Illuminate\Support\Arr;

class Permissions
{

    public static function createPermissions($module)
    {
        $config = config('laratrust_seeder.modules');
        $mapPermission = collect(config('laratrust_seeder.permissions_map'));

        $reqModule = array_filter($config, function ($mod) use ($module) {
            return $mod === $module->name;
        }, ARRAY_FILTER_USE_KEY);

        if (count($reqModule) > 0) {
            // create permissions
            $permissions = current($reqModule[$module->name]);

            foreach (explode(',', $permissions) as $p => $perm) {
                $permissionValue = $mapPermission->get($perm);

                Permission::firstOrCreate([
                    'name' => strtolower($permissionValue . '_' . $module->name),
                    'display_name' => ucfirst($permissionValue) . ' ' . ucwords(str_replace('_', ' ', $module->name)),
                    'description' => ucfirst($permissionValue) . ' ' . ucwords(str_replace('_', ' ', $module->name)),
                    'module_id' => $module->id
                ]);
            }
        }
    }

    public static function assignPermissions(Role $role)
    {
        $config = config('laratrust_seeder.modules');
        $mapPermission = collect(config('laratrust_seeder.permissions_map'));

        $permissionsArr = [];

        foreach ($config as $module => $rolePermission)
        {
            if (Arr::has($rolePermission, $role->name)) {
                $permissions = $rolePermission[$role->name];

                foreach (explode(',', $permissions) as $p => $perm) {
                    $permissionValue = $mapPermission->get($perm);

                    $permissionsArr[] = Permission::where([
                        'name' => strtolower($permissionValue . '_' . $module),
                    ])->first()->id;
                }
            }
        }

        $role->syncPermissions($permissionsArr);
    }

    public static function getModules(Role $role)
    {

        $config = config('laratrust_seeder.modules');

        $modules = [];

        foreach ($config as $module => $value)
        {
            if (in_array($role->name, array_keys($value))) {
                array_push($modules, $module);
            }
        }

        return $modules;
    }

}

