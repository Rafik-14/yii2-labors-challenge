<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use kartik\date\DatePicker;
use kartik\checkbox\CheckboxX;

/** @var yii\web\View $this */
/** @var app\models\Labors $model */
/** @var yii\bootstrap5\ActiveForm $form */

// Inline SVG icons: the Kartik defaults use Font Awesome, which this project does not load.
$calendarIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v1h14V3a1 1 0 0 0-1-1H2zm13 3H1v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V5z"/></svg>';
$clearIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';
?>

<div class="card border-0 shadow-sm rounded-4 p-4 p-md-4 labors-card">
    <div class="card-body">

        <?php $form = ActiveForm::begin([
            'id' => 'labors-form',
            // Yii's (translated) client validation only; no browser-language HTML5 tooltips
            'options' => ['novalidate' => true],
            'fieldConfig' => [
                'labelOptions' => ['class' => 'form-label fw-semibold'],
            ],
        ]); ?>

        <div class="row g-3">
            <div class="col-md-6">
                <?= $form->field($model, 'first_name')->textInput([
                    'maxlength' => true,
                    'class' => 'form-control form-control-lg fs-6',
                    'placeholder' => Yii::t('app', 'Enter first name')
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'last_name')->textInput([
                    'maxlength' => true,
                    'class' => 'form-control form-control-lg fs-6',
                    'placeholder' => Yii::t('app', 'Enter last name')
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'email')->textInput([
                    'type' => 'email',
                    'maxlength' => true,
                    'class' => 'form-control form-control-lg fs-6',
                    'placeholder' => Yii::t('app', 'Enter email address')
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'ip_address')->textInput([
                    'maxlength' => true,
                    'class' => 'form-control form-control-lg fs-6',
                    'placeholder' => Yii::t('app', 'Enter IP address')
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'working_minutes')->textInput([
                    'type' => 'number',
                    'min' => 0,
                    'max' => $model::MAX_WORKING_MINUTES,
                    'class' => 'form-control form-control-lg fs-6',
                    'placeholder' => Yii::t('app', 'Enter working minutes')
                ]) ?>
            </div>

            <div class="col-md-6">
                <?= $form->field($model, 'working_date')->widget(DatePicker::class, [
                    // English month names are required by the spec (23-Feb-1982) and by the server-side parser.
                    'language' => 'en',
                    'options' => [
                        // the attribute holds the DB value (Y-m-d H:i:s); show it in the picker format
                        'value' => $model->getWorkingDateInput(),
                        'placeholder' => Yii::t('app', 'Select date (e.g. 23-Feb-1982)...'),
                        'class' => 'form-control form-control-lg fs-6',
                        'autocomplete' => 'off',
                    ],
                    'pickerIcon' => $calendarIcon,
                    'removeIcon' => $clearIcon,
                    'pickerButton' => ['title' => Yii::t('app', 'Select date')],
                    'removeButton' => ['title' => Yii::t('app', 'Clear date')],
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-M-yyyy',
                        'todayHighlight' => true
                    ]
                ]) ?>
            </div>

            <div class="col-12">
                <div class="p-3 bg-body-tertiary rounded-3 border border-secondary-subtle">
                    <?= $form->field($model, 'need_work', [
                        'labelOptions' => ['class' => 'form-label fw-semibold mb-2 d-block'],
                    ])->widget(CheckboxX::class, [
                        'pluginOptions' => [
                            'threeState' => false,
                        ],
                    ]) ?>
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
