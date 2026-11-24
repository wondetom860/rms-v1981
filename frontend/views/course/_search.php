<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'course_code') ?>

    <?= $form->field($model, 'course_title') ?>

    <?= $form->field($model, 'course_crdthr') ?>

    <?= $form->field($model, 'course_dept_relId') ?>

    <?php // echo $form->field($model, 'course_lecture_hour') ?>

    <?php // echo $form->field($model, 'course_lab_hour') ?>

    <?php // echo $form->field($model, 'course_tutorial_hour') ?>

    <?php // echo $form->field($model, 'course_created_by') ?>

    <?php // echo $form->field($model, 'course_created_at') ?>

    <?php // echo $form->field($model, 'course_updated_by') ?>

    <?php // echo $form->field($model, 'course_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
