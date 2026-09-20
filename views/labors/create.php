<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Labors $model */

$this->title = Yii::t('app', 'Create Labors');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Labors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="labors-create max-width-900 mx-auto">

    <div class="mb-4">
        <h1 class="h3 fw-bold mb-1"><?= Html::encode($this->title) ?></h1>
        <p class="text-secondary small mb-0">Fill out the details below to add a new labor record.</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
