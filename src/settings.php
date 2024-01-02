<?php
return [
    'settings' => [
        'displayErrorDetails' => false, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'matrix_api',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        // DB settings
        'db' => [
            'driver'    => 'pgsql',
            'host'      => '191.242.48.28',
            'database'  => 'db_datainformation',
            'username'  => 'fmlindolfo',
            'password'  => 'fmlindolfo',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],

        // Secret
        'secretKey' => '627539ed9ee1e742451b695434d76ef02e612061',

    ],
];
