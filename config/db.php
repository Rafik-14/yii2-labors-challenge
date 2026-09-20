<?php

return [
    'class' => \yii\db\Connection::class,
    'dsn' => 'mysql:host=' . getenv('DB_HOST', '127.0.0.1') . ';dbname=' . getenv('DB_NAME', 'yii2_labors_db'),
    'username' => getenv('DB_USER', 'root'),
    'password' => getenv('DB_PASSWORD', ''),
    'charset' => 'utf8mb4',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
