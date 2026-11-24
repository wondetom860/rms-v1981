<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\TRANSTUD\models\ExemptedCourseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="exempted-course-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ts_relid') ?>

    <?= $form->field($model, 'course_code') ?>

    <?= $form->field($model, 'course_name') ?>

    <?= $form->field($model, 'course_hours') ?>

    <?php // echo $form->field($model, 'grade_obtained') ?>

    <?php // echo $form->field($model, 'grade_point') ?>

    <?php // echo $form->field($model, 'grade_point_sum') ?>

    <?php // echo $form->field($model, 'equivalent_course_ids') ?>

    <?php // echo $form->field($model, 'approved') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'deleted_at') ?>

    <?php // echo $form->field($model, 'deleted_by') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
