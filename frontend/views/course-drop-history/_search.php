<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseDropHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-drop-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cdh_srd2_relId') ?>

    <?= $form->field($model, 'cdh_std_relId') ?>

    <?= $form->field($model, 'cdh_course_relId') ?>

    <?= $form->field($model, 'cdh_acadYear_relId') ?>

    <?php // echo $form->field($model, 'cdh_sem_relId') ?>

    <?php // echo $form->field($model, 'cdh_group_relId') ?>

    <?php // echo $form->field($model, 'cdh_clslvl_relId') ?>

    <?php // echo $form->field($model, 'cdh_approved') ?>

    <?php // echo $form->field($model, 'cdh_approved_by') ?>

    <?php // echo $form->field($model, 'cdh_status') ?>

    <?php // echo $form->field($model, 'cdh_created_at') ?>

    <?php // echo $form->field($model, 'cdh_updated_at') ?>

    <?php // echo $form->field($model, 'cdh_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
