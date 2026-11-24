<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcActiveProgramsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ac-active-programs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'academic_year_relId') ?>

    <?= $form->field($model, 'admission_relId') ?>

    <?= $form->field($model, 'program_relId') ?>

    <?= $form->field($model, 'dept_relId') ?>

    <?php // echo $form->field($model, 'class_level_relId') ?>

    <?php // echo $form->field($model, 'semester_relId') ?>

    <?php // echo $form->field($model, 'spec_relId') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
