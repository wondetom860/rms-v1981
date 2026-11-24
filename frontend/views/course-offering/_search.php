<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOfferingTempSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-offering-temp-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cot_acadYear_relId') ?>

    <?= $form->field($model, 'cot_sem_relId') ?>

    <?= $form->field($model, 'cot_adm_relId') ?>

    <?= $form->field($model, 'cot_prog_relId') ?>

    <?php // echo $form->field($model, 'cot_dept_relId') ?>

    <?php // echo $form->field($model, 'cot_clslvel_relId') ?>

    <?php // echo $form->field($model, 'cot_spec_relId') ?>

    <?php // echo $form->field($model, 'cot_cocat_IDS') ?>

    <?php // echo $form->field($model, 'cot_created_at') ?>

    <?php // echo $form->field($model, 'cot_created_by') ?>

    <?php // echo $form->field($model, 'cot_updated_at') ?>

    <?php // echo $form->field($model, 'cot_updated_by') ?>

    <?php // echo $form->field($model, 'cot_locked') ?>

    <?php // echo $form->field($model, 'cot_registrarApproved') ?>

    <?php // echo $form->field($model, 'cot_registrarApproved_at') ?>

    <?php // echo $form->field($model, 'cot_registrarApproved_by') ?>

    <?php // echo $form->field($model, 'cot_acProg_relId') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
