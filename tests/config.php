<?php
return [
    'application' => [
        'environment' => \Vegas\Constants::TEST_ENV,
        'serviceDir' => APP_ROOT . '/app/services/',
        'configDir' => APP_ROOT . '/app/config/',
        'libraryDir' => APP_ROOT. '/lib/',
        'pluginDir' => APP_ROOT . '/app/plugins/',
        'moduleDir' => APP_ROOT . '/app/modules/',
        'taskDir' => APP_ROOT . '/app/tasks/',
        'baseUri' => '/',
        'language' => 'nl_NL',
        'view' => [
            'cacheDir' => APP_ROOT . '/cache/',
            'layout' => 'main',
            'layoutsDir' => APP_ROOT . '/app/layouts/',
            'partialsDir' => APP_ROOT . '/app/layouts/partials/',
            'compileAlways' => true
        ]
    ]
];