<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Labors $model */

$this->title = 'Update Labors: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Labors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="labors-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
