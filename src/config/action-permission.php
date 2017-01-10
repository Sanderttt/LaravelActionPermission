<?php

return [

    'included_request_methods' => [
        'POST',
        'GET'
    ],

    'controller_name_parts_to_be_removed' => [
        'Controller'
    ],

    'excluded_domains' => [
        'dashboard.zien24.dev'
    ],

    'translation_prefix' => 'actions',

    'cache_key' => 'permissions',
];
