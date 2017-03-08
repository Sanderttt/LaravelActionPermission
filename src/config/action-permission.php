<?php

return [

    'included_request_methods' => [
        'POST',
        'GET'
    ],

    /*
     * If you are using a basename which shouldnt be shown
     */
    'controller_name_parts_to_be_removed' => [
        'Controller'
    ],

    /*
     * If you are using multiple domains and not all of them should be checked
     */
    //ToDo add publish config
    'excluded_domains' => [
        'dashboard.zien24.dev',
        'dashboard.test.zien24.com',
        'dashboard.zien24.nl',
    ],

    'translation_prefix' => 'actions',

    'cache_key' => 'permissions',
];
