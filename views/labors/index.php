<?php

use app\models\Labors;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Labors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="labors-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Labors'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
                'label' => Yii::t('app', 'Need Work'),
            ],
            'working_minutes',
            [
                'attribute' => 'working_date',
                'format' => ['date', 'php:d-M-Y H:i:s'],
                'label' => Yii::t('app', 'Working Date'),
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update}',
                'urlCreator' => function ($action, Labors $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
