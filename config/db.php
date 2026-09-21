<?php

require_once __DIR__ . '/env.php';

return [
    'class' => \yii\db\Connection::class,
    'dsn' => 'mysql:host=' . env('DB_HOST', '127.0.0.1') . ';dbname=' . env('DB_NAME', 'yii2_labors_db'),
    'username' => env('DB_USER', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',

    // Cache table metadata outside of debug mode to avoid SHOW COLUMNS on every request.
    'enableSchemaCache' => defined('YII_DEBUG') && !YII_DEBUG,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
];
