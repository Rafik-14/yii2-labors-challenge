<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$items = [
    [
        'label' => Yii::t('app', 'Home'),
        'url' => ['/site/index'],
    ],
    [
        'label' => Yii::t('app', 'Labors'),
        'url' => ['/labors/index'],
    ],
    [
        'label' => Yii::t('app', 'Create Labors'),
        'url' => ['/labors/create'],
    ],
];

?>
<header id="header">
    <?php NavBar::begin(
        [
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
        ],
    ) ?>
    <?= Nav::widget(
        [
            'options' => ['class' => 'navbar-nav me-auto'],
            'encodeLabels' => false,
            'items' => $items,
        ],
    ) ?>
    <?= Html::button(
        'Theme',
        [
            'id' => 'theme-toggle',
            'class' => 'btn btn-outline-light btn-sm ms-2',
            'aria-label' => 'Switch theme',
        ],
    ) ?>
    <?php NavBar::end() ?>
</header>
