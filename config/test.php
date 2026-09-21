<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';
// Reuse the web application's routing, parsing, translation and DI settings so tests exercise the real runtime.
$web = require __DIR__ . '/web.php';

/**
 * Application configuration shared by all test types
 */
return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        \app\tests\Support\MailerBootstrap::class,
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => $web['language'],
    'container' => [
        'definitions' => $web['container']['definitions'],
    ],
    'components' => [
        'db' => $db,
        'i18n' => $web['components']['i18n'],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'messageClass' => \yii\symfonymailer\Message::class,
            'useFileTransport' => true,
            'viewPath' => '@app/mail',
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => $web['components']['urlManager'],
        'user' => [
            'identityClass' => \app\models\User::class,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
            'parsers' => $web['components']['request']['parsers'],
        ],
    ],
    'params' => $params,
];
