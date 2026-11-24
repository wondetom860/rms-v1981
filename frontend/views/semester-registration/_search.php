<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2Search */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration2-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sr_academicYear_relId') ?>

    <?= $form->field($model, 'sr_semester_relId') ?>

    <?= $form->field($model, 'sr_student_relId') ?>

    <?= $form->field($model, 'sr_courses') ?>

    <?php // echo $form->field($model, 'sr_status') ?>

    <?php // echo $form->field($model, 'sr_setOn') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
