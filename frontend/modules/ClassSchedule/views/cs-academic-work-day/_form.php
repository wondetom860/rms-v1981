<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsAcademicWorkDay */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cs-academic-work-day-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'day_name')->textInput(['maxlength' => true])->hint('Monday') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'class_start_at')->textInput()->hint('8:30') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'duration_one_class')->textInput()->hint('duration of one class in minutes') ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'lunch_time')->textInput(['maxlength' => true])->hint('12:30-13:20') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'class_end_time')->textInput()->hint('17:20') ?>
        </div>
        
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group pull-right">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>

        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
