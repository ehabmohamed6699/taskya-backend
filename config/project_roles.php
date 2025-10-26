<?php

return [
    'roles' => [
        'owner' => 'Owner',
        'manager' => 'Manager',
        'member' => 'Member',
        'viewer' => 'Viewer',
    ],

    'permissions' => [
        'create_task',
        'edit_task',
        'delete_task',
        'add_comment',
        'edit_comment',
        'delete_comment',
        'add_member',
        'remove_member',
        'change_role',
        'update_project',
        'delete_project',
    ],

    'role_permissions' => [
        'owner' => ['*'], // كل الصلاحيات
        'manager' => [
            'create_task','edit_task','delete_task',
            'add_comment','edit_comment','delete_comment',
            'add_member','remove_member','change_role'
        ],
        'member' => [
            'create_task','edit_task','delete_task',
            'add_comment','edit_comment','delete_comment',
        ],
        'viewer' => [
            'add_comment','edit_comment','delete_comment',
        ],
    ],
];
