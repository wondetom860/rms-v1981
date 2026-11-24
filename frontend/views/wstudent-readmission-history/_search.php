<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\WStudentReadmissionHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wstudent-readmission-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'srh_acadYear_relId') ?>

    <?= $form->field($model, 'srh_sem_relId') ?>

    <?= $form->field($model, 'srh_swh_relId') ?>

    <?= $form->field($model, 'srh_std_relId') ?>

    <?php // echo $form->field($model, 'srh_return_back_on') ?>

    <?php // echo $form->field($model, 'srh_HOD_approved') ?>

    <?php // echo $form->field($model, 'srh_HOD_approved_at') ?>

    <?php // echo $form->field($model, 'srh_HOD_approved_by') ?>

    <?php // echo $form->field($model, 'srh_approved') ?>

    <?php // echo $form->field($model, 'srh_approved_at') ?>

    <?php // echo $form->field($model, 'srh_approved_by') ?>

    <?php // echo $form->field($model, 'srh_return_back_letter') ?>

    <?php // echo $form->field($model, 'remark_HOD') ?>

    <?php // echo $form->field($model, 'remark_registrar') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
