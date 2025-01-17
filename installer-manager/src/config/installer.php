<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Server Requirements
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel server requirements, you can add as many
    | as your application require, we check if the extension is enabled
    | by looping through the array and run "extension_loaded" on it.
    |
    */
    'requirements' => [
        'php' => '8.0.0',
        'extensions' => [
            'pdo',
            'pdo_mysql',
            'json',
            'openssl',
            'mbstring',
            'tokenizer',
            'xml',
            'ctype',
            'fileinfo',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Folders Permissions
    |--------------------------------------------------------------------------
    |
    | This is the default Laravel folders permissions, if your application
    | requires more permissions just add them to the array list below.
    |
    */
    'permissions' => [
        'storage/framework/' => '775',
        'storage/logs/' => '775',
        'bootstrap/cache/' => '775',
    ],

    /*
    |--------------------------------------------------------------------------
    | Installation Steps
    |--------------------------------------------------------------------------
    |
    | The installation steps in order. These can be modified to add or remove steps
    | as needed for your application.
    |
    */
    'steps' => [
        'requirements' => [
            'name' => 'Requirements',
            'route' => 'installer.requirements',
        ],
        'database' => [
            'name' => 'Database',
            'route' => 'installer.database',
        ],
    ],
];