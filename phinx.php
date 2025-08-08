<?php

return \magein\migration\PhinxConfig::config(
    [
        'environments' => [
            'default_migration_table' => 'phinx_log',
            'default_environment' => 'development',
            'production' => [
                'adapter' => 'mysql',
                'host' => 'localhost',
                'name' => 'production_db',
                'user' => 'root',
                'pass' => '',
                'port' => 3306,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'development' => [
                'adapter' => 'mysql',
                'host' => '127.0.0.1',
                'name' => 'demo',
                'user' => 'root',
                'pass' => '',
                'port' => 3306,
                'charset' => 'utf8',
            ],
            'testing' => [
                'adapter' => 'sqlite',
                'name' => './tests/_data/database',
                'memory' => false
            ]
        ],
    ]
);