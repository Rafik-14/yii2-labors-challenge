<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

// The cookie signing key comes from the environment. A fixed key is only acceptable for local development and tests.
$cookieValidationKey = env('COOKIE_VALIDATION_KEY', '');
if ($cookieValidationKey === '') {
    if (!YII_ENV_DEV && !YII_ENV_TEST) {
        throw new \yii\base\InvalidConfigException('The COOKIE_VALIDATION_KEY environment variable must be set.');
    }
    $cookieValidationKey = 'insecure-local-development-key';
}

$config = [
    'id' => 'basic',
    'language' => 'hu-HU',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'container' => [
        'definitions' => [
            // Bootstrap 5 markup (.page-item / .page-link) for every GridView/ListView pager.
            \yii\widgets\LinkPager::class => \yii\bootstrap5\LinkPager::class,
        ],
        'singletons' => [
            \yii\mail\MailerInterface::class => [
                'class' => \yii\symfonymailer\Mailer::class,
                // send all mails to a file by default.
                'useFileTransport' => true,
                'viewPath' => '@app/mail',
            ],
        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => $cookieValidationKey,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
            ],
        ],
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'user' => [
            'identityClass' => \app\models\User::class,
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => \yii\mail\MailerInterface::class,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                // other verbs still reach the action and are rejected with 405 by its VerbFilter
                'POST api/works' => 'api/works',
                'labors' => 'labors/index',
                'labors/<action:\w+>' => 'labors/<action>',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment (never enable YII_ENV=dev in production)
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => \yii\debug\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => \yii\gii\Module::class,
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
