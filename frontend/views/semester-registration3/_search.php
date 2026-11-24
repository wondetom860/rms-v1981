<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration3Search */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration3-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'semreg_stud_relId') ?>

    <?= $form->field($model, 'semreg_sem_relId') ?>

    <?= $form->field($model, 'semreg_coff_relId') ?>

    <?= $form->field($model, 'semreg_coff_relIds') ?>

    <?php // echo $form->field($model, 'semreg_mark_obtained') ?>

    <?php // echo $form->field($model, 'semreg_created_on') ?>

    <?php // echo $form->field($model, 'semreg_created_by') ?>

    <?php // echo $form->field($model, 'semreg_updated_on') ?>

    <?php // echo $form->field($model, 'semreg_updated_by') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
