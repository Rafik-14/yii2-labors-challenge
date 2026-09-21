<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = Yii::t('app', 'My Yii Application');
$this->params['meta_description'] = 'A high-performance PHP framework best for developing web applications. Fast, secure, and professional.';
$this->params['meta_keywords'] = 'yii, yii2, php, framework, web application, high-performance';
?>
<div class="site-index">

    <!-- Hero banner with Yii gradient -->
    <div class="hero-banner text-white rounded-4 p-5 mb-4 position-relative overflow-hidden">
        <?= Html::img(Yii::getAlias('@web/images/yii3_full_white_for_dark.svg'), [
            'alt' => '',
            'class' => 'd-none d-lg-block position-absolute hero-logo',
        ]) ?>
        <div class="position-relative">
            <h1 class="display-5 fw-bold mb-3"><?= Yii::t('app', 'Build with Yii Framework') ?></h1>
            <p class="lead opacity-75 mb-4 hero-lead">
                <?= Yii::t('app', 'A high-performance PHP framework best for developing web applications. Fast, secure, and professional.') ?>
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <?= Html::a(
                    Yii::t('app', 'Labors'),
                    ['/labors/index'],
                    [
                        'class' => 'btn btn-light btn-lg fw-semibold px-4',
                    ],
                ) ?>
                <?= Html::a(
                    Yii::t('app', 'Create Labors'),
                    ['/labors/create'],
                    [
                        'class' => 'btn btn-success btn-lg fw-semibold px-4',
                    ],
                ) ?>
            </div>
        </div>
    </div>

    <!-- Extensions grid -->
    <div class="row g-3">
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0">yii2-debug</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        <?= Yii::t('app', 'Debug toolbar and debugger for Yii2. Inspect logs, database queries, request data, and application performance in real time.') ?>
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        Yii::t('app', 'Learn more') . ' &raquo;',
                        'https://www.yiiframework.com/extension/yiisoft/yii2-debug',
                        [
                            'aria-label' => Yii::t('app', 'Learn more about {name}', ['name' => 'yii2-debug']),
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0">yii2-gii</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        <?= Yii::t('app', 'Automatic code generator for models, controllers, CRUD, forms, and modules. Boost your productivity with scaffolding.') ?>
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        Yii::t('app', 'Learn more') . ' &raquo;',
                        'https://www.yiiframework.com/extension/yiisoft/yii2-gii',
                        [
                            'aria-label' => Yii::t('app', 'Learn more about {name}', ['name' => 'yii2-gii']),
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0">yii2-queue</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        <?= Yii::t('app', 'Asynchronous job queue with support for DB, Redis, AMQP, Beanstalk, and SQS drivers. Run background tasks with ease.') ?>
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        Yii::t('app', 'Learn more') . ' &raquo;',
                        'https://www.yiiframework.com/extension/yiisoft/yii2-queue',
                        [
                            'aria-label' => Yii::t('app', 'Learn more about {name}', ['name' => 'yii2-queue']),
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0">yii2-redis</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        <?= Yii::t('app', 'Redis integration providing cache, session, and ActiveRecord support. Leverage in-memory storage for blazing-fast data access.') ?>
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        Yii::t('app', 'Learn more') . ' &raquo;',
                        'https://www.yiiframework.com/extension/yiisoft/yii2-redis',
                        [
                            'aria-label' => Yii::t('app', 'Learn more about {name}', ['name' => 'yii2-redis']),
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0">yii2-elasticsearch</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        <?= Yii::t('app', 'Elasticsearch integration with ActiveRecord and query builder. Add powerful full-text search capabilities to your application.') ?>
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        Yii::t('app', 'Learn more') . ' &raquo;',
                        'https://www.yiiframework.com/extension/yiisoft/yii2-elasticsearch',
                        [
                            'aria-label' => Yii::t('app', 'Learn more about {name}', ['name' => 'yii2-elasticsearch']),
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-3 extension-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="h6 fw-bold mb-0">yii2-symfonymailer</h3>
                    </div>
                    <p class="text-body-secondary small mb-0">
                        <?= Yii::t('app', 'Email sending integration powered by Symfony Mailer. Compose and deliver rich HTML emails with attachments and templates.') ?>
                    </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <?= Html::a(
                        Yii::t('app', 'Learn more') . ' &raquo;',
                        'https://github.com/yiisoft/yii2-symfonymailer',
                        [
                            'aria-label' => Yii::t('app', 'Learn more about {name}', ['name' => 'yii2-symfonymailer']),
                            'class' => 'btn btn-sm btn-outline-secondary',
                            'rel' => 'noopener',
                            'target' => '_blank',
                        ],
                    ) ?>
                </div>
            </div>
        </div>
    </div>

</div>
