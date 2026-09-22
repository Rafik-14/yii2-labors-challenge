<?php

declare(strict_types=1);

namespace app\controllers;

use yii\web\Controller;
use yii\web\ErrorAction;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }
}
