<?php

use app\models\Labors;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\LaborsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Labors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="labors-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Labors'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-bordered align-middle'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'first_name',
            'last_name',
            'email:email',
            'ip_address',
            [
                'attribute' => 'need_work',
                'format' => 'boolean',
                'filter' => [1 => Yii::$app->formatter->asBoolean(true), 0 => Yii::$app->formatter->asBoolean(false)],
            ],
            'working_minutes',
            [
                'attribute' => 'working_date',
                'value' => static fn (Labors $model) => $model->getWorkingDateDisplay(),
                'filterInputOptions' => ['class' => 'form-control', 'placeholder' => '2021-05-19'],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update}',
                'urlCreator' => function ($action, Labors $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>
    </div>

</div>
