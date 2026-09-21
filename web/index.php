<?php

declare(strict_types=1);

// Built-in PHP CLI web server router support: serve physical files directly
if (php_sapi_name() === 'cli-server') {
    $filePath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($filePath)) {
        return false; // Tells PHP CLI server to serve the static file directly
    }
}

require __DIR__ . '/../config/env.php';

// Secure by default: debug mode and the dev environment must be switched on explicitly (e.g. in .env).
defined('YII_DEBUG') or define('YII_DEBUG', env('YII_DEBUG') === 'true');
defined('YII_ENV') or define('YII_ENV', env('YII_ENV', 'prod'));

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
