<?php

return [
    'modules' => [
        'company' => [
            'superadmin' => 'c,r,u,d'
        ],
        'package' => [
            'superadmin' => 'c,r,u,d'
        ],
        'ticket' => [
            'superadmin' => 'c,r,u,d',
            'administrator' => 'c,r,u,d',
        ],
        'location' => [
            /** 'administrator' => 'c,r,u,d', */
            'superadmin' => 'c,r,u,d'
        ],
        'category' => [
            'administrator' => 'c,r,u,d',
            'superadmin' => 'c,r,u,d'
        ],
        'business_service' => [
            'administrator' => 'c,r,u,d',
        ],
        'customer' => [
            'administrator' => 'c,r,u,d',
        ],
        'employee' => [
            'administrator' => 'c,r,u,d',
        ],
        'coupon' => [
            'administrator' => 'c,r,u,d',
            'superadmin' => 'c,r,u,d'
        ],
        'deal' => [
            'administrator' => 'c,r,u,d',
        ],
        'employee_group' => [
            'administrator' => 'c,r,u,d',
        ],
        'booking' => [
            'administrator' => 'c,r,u,d',
            'employee' => 'r,u',
            'customer' => 'r,u',
        ],
        'report' => [
            'administrator' => 'c,r,u,d',
        ],
        'employee_leave' => [
            'administrator' => 'c,r,u,d',
        ],
        'employee_schedule' => [
            'administrator' => 'c,r,u,d',
        ],
        'settings' => [
            'superadmin' => 'm',
            'administrator' => 'm',
        ],
        'article' => [
            'superadmin' => 'm',
            'administrator' => 'c,r,u',
        ],
        'tout' => [
            'superadmin' => 'm',
            'administrator' => 'c,r,u',
        ],
    ],
    'permission_structure' => [
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
        'm' => 'manage'
    ],
    'default_roles' => ['superadmin', 'administrator', 'employee','agent']
];
