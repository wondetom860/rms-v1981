<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\ReadmissionHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="readmission-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'rh_student_relId') ?>

    <?= $form->field($model, 'rh_academicCal_relId') ?>

    <?= $form->field($model, 'rh_semester_reiId') ?>

    <?= $form->field($model, 'rh_request') ?>

    <?php // echo $form->field($model, 'rh_status') ?>

    <?php // echo $form->field($model, 'rh_updatedBy') ?>

    <?php // echo $form->field($model, 'rh_updatedAt') ?>

    <?php // echo $form->field($model, 'rh_createdAt') ?>

    <?php // echo $form->field($model, 'rh_createdBy') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
