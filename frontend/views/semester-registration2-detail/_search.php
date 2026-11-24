<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2DetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration2-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'srd2_semreg_relId') ?>

    <?= $form->field($model, 'srd2_coff_relId') ?>

    <?= $form->field($model, 'srd2_course_relId') ?>

    <?= $form->field($model, 'srd2_grade_relId') ?>

    <?php // echo $form->field($model, 'srd2_coadd_flag') ?>

    <?php // echo $form->field($model, 'srd2_corptd_flag') ?>

    <?php // echo $form->field($model, 'srd2_codrpd_relId') ?>

    <?php // echo $form->field($model, 'srd2_corProject') ?>

    <?php // echo $form->field($model, 'srd2_enrollApproved') ?>

    <?php // echo $form->field($model, 'srd2_enrollApproved_by') ?>

    <?php // echo $form->field($model, 'srd2_enrolApprovrd_at') ?>

    <?php // echo $form->field($model, 'srd2_group_relId') ?>

    <?php // echo $form->field($model, 'srd2_updated_by') ?>

    <?php // echo $form->field($model, 'srd2_updated_at') ?>

    <?php // echo $form->field($model, 'srd2_created_at') ?>

    <?php // echo $form->field($model, 'srd2_created_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
