<?php

return [
    [
        'name' => 'administrator',
        'title' => 'User',
        'icon' => 'flaticon-user',
        'sidebar_menu_active' => 'users',
        'vue_route_name' => 'Users',
        'model_list' => [
            [
                'name' => 'add',
                'title' => 'Add User',
                'icon' => 'la la-plus',
                'url' => '#/user-create',
                'sidebar_submenu_active' => 'new-user',
                'vue_route_name' => 'UserCreate',
                'view_list' => []
            ], 
            [
                'name' => 'list',
                'title' => 'User List',
                'icon' => 'flaticon-list-1',
                'url' => '#/user-list',
                'sidebar_submenu_active' => 'user-list',
                'vue_route_name' => 'UserList',
                'view_list' => []
            ]
        ]
    ],
    [
        'name' => 'campaign',
        'title' => 'Campaign',
        'icon' => 'flaticon-users',
        'sidebar_menu_active' => 'campaign',
        'vue_route_name' => 'Campaign',
        'model_list' => [
            [
                'name' => 'add',
                'title' => 'Add Campaign Profile',
                'icon' => 'la la-plus',
                'url' => '#/campaign-profile-create',
                'sidebar_submenu_active' => 'campaign-profile-create',
                'vue_route_name' => 'CampaignProfileCreate',
                'view_list' => []
            ],
            [
                'name' => 'list',
                'title' => 'Campaign Profile List',
                'icon' => 'flaticon-list-1',
                'url' => '#/campaign-profile-list',
                'sidebar_submenu_active' => 'campaign-profile-list',
                'vue_route_name' => 'CampaignProfileList',
                'view_list' => [],
              
            ]
        ]
            
    ],
    [
        'name' => 'email_template',
        'title' => 'Email Template',
        'icon' => 'flaticon-multimedia-3',
        'sidebar_menu_active' => 'email_template',
        'vue_route_name' => 'Email Template',
        'url' => 'email-template/template-builder',
        'model_list' => []
    ],


     
];