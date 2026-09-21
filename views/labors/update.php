<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Labors $model */

$this->title = Yii::t('app', 'Update Labors: {name}', ['name' => $model->getFullName()]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Labors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getFullName(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="labors-update max-width-900 mx-auto">

    <h1 class="h3 fw-bold mb-4"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
