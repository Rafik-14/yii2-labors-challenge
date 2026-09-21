<?php

$db = require __DIR__ . '/db.php';
// Tests run against a separate database so they never touch development data.
$db['dsn'] = 'mysql:host=' . env('DB_HOST', '127.0.0.1') . ';dbname=' . env('TEST_DB_NAME', 'yii2_labors_test');

return $db;
