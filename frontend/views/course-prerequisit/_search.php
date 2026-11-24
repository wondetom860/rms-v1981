<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CoursePrerequisitSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-prerequisit-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'preq_adm_relId') ?>

    <?= $form->field($model, 'preq_prog_relId') ?>

    <?= $form->field($model, 'preq_dept_relId') ?>

    <?= $form->field($model, 'preq_spec_relId') ?>

    <?php // echo $form->field($model, 'preq_main_course_relId') ?>

    <?php // echo $form->field($model, 'preq_preq_course_relId') ?>

    <?php // echo $form->field($model, 'preq_created_by') ?>

    <?php // echo $form->field($model, 'preq_created_at') ?>

    <?php // echo $form->field($model, 'preq_updated_by') ?>

    <?php // echo $form->field($model, 'preq_updated_at') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
