<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\checkbox\CheckboxX;

/** @var yii\web\View $this */
/** @var app\models\Labors $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="card border-0 shadow-sm rounded-4 p-4 p-md-4 labors-card">
    <div class="card-body">

        <?php $form = ActiveForm::begin(['id' => 'labors-form']); ?>

        <div class="row g-3">
            <div class="col-md-6">
                <?= $form->field($model, 'first_name')->textInput([
                    'maxlength' => true,
                    'class' => 'form-control form-control-lg fs-6',
                    'placeholder' => Yii::t('app', 'Enter first name')
                ])->label(Yii::t('app', 'First Name'), ['class' => 'form-label fw-semibold']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'last_name')->textInput([
                    'maxlength' => true,
                    'class' => 'form-control form-control-lg fs-6',
                    'placeholder' => Yii::t('app', 'Enter last name')
                ])->label(Yii::t('app', 'Last Name'), ['class' => 'form-label fw-semibold']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'email')->textInput([
                    'maxlength' => true,
                    'class' => 'form-control form-control-lg fs-6',
                    'placeholder' => Yii::t('app', 'Enter email address')
                ])->label(Yii::t('app', 'Email'), ['class' => 'form-label fw-semibold']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'ip_address')->textInput([
                    'maxlength' => true,
                    'class' => 'form-control form-control-lg fs-6',
                    'placeholder' => Yii::t('app', 'Enter IP address')
                ])->label(Yii::t('app', 'IP Address'), ['class' => 'form-label fw-semibold']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'working_minutes')->textInput([
                    'type' => 'number',
                    'class' => 'form-control form-control-lg fs-6',
                    'placeholder' => Yii::t('app', 'Enter working minutes')
                ])->label(Yii::t('app', 'Working Minutes'), ['class' => 'form-label fw-semibold']) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'working_date')->widget(DatePicker::class, [
                    'options' => [
                        'placeholder' => Yii::t('app', 'Select date (e.g. 23-Feb-1982)...'),
                        'class' => 'form-control form-control-lg fs-6'
                    ],
                    'buttonOptions' => [
                        'label' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1H2zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V5z"/></svg>',
                        'class' => 'btn',
                    ],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-M-yyyy',
                        'todayHighlight' => true
                    ]
                ])->label(Yii::t('app', 'Working Date'), ['class' => 'form-label fw-semibold']) ?>
            </div>

            <div class="col-12">
                <div class="p-3 bg-body-tertiary rounded-3 border border-secondary-subtle">
                    <?= $form->field($model, 'need_work')->widget(CheckboxX::class, [
                        'pluginOptions' => [
                            'threeState' => false,
                        ],
                    ])->label(Yii::t('app', 'Need Work'), ['class' => 'form-label fw-semibold mb-2 d-block']) ?>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 mt-4 pt-2 border-top">
            <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-lg px-4 fs-6 fw-semibold']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-outline-secondary btn-lg px-4 fs-6']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
